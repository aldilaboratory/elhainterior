<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use Midtrans\Notification;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('midtrans.webhook.raw', ['payload' => $request->all()]);

        // 1) Verifikasi signature (WAJIB)
        $serverKey = config('midtrans.server_key');
        $recvSig   = (string) ($request->input('signature_key') ?? $request->header('X-Callback-Signature'));
        $base      = (string) $request->input('order_id')
                   . (string) $request->input('status_code')
                   . (string) $request->input('gross_amount')
                   . $serverKey;
        $calcSig   = hash('sha512', $base);

        if (!hash_equals($calcSig, $recvSig)) {
            Log::warning('midtrans.webhook.invalid_signature', ['expected'=>$calcSig,'received'=>$recvSig]);
            return response('invalid signature', 403);
        }

        // 2) Ambil notifikasi dari SDK (butuh Config sudah di-boot)
        $notif = new Notification();

        $orderId   = $notif->order_id;
        $trxStatus = $notif->transaction_status;   // settlement|pending|capture|deny|cancel|expire|refund
        $fraud     = $notif->fraud_status;         // accept|challenge|deny
        $payType   = $notif->payment_type;
        $trxId     = $notif->transaction_id ?? null;

        // 3) Cari order (by order_code atau midtrans_order_id)
        $order = Order::where('order_code', $orderId)
            ->orWhere('midtrans_order_id', $orderId)
            ->first();

        if (!$order) {
            Log::warning('midtrans.webhook.order_not_found', ['order_id' => $orderId]);
            return response('order not found', 404);
        }

        // 4) Map status → update order
        $paid = false;
        switch ($trxStatus) {
            case 'capture':
                $paid = ($fraud === 'accept' || $fraud === null);
                $order->payment_status = $paid ? 'paid' : 'pending';
                break;
            case 'settlement':
                $paid = true;
                $order->payment_status = 'paid';
                break;
            case 'pending':
                $order->payment_status = 'pending'; break;
            case 'deny':
            case 'cancel':
            case 'expire':
                $order->payment_status = 'failed'; break;
            case 'refund':
                $order->payment_status = 'refunded'; break;
        }

        if ($paid && !$order->paid_at) {
            $order->paid_at = now();
        }

        $order->fill([
            'midtrans_order_id'       => $orderId,
            'midtrans_transaction_id' => $trxId,
            'midtrans_payment_type'   => $payType,
            'midtrans_status'         => $trxStatus,
            'fraud_status'            => $fraud ?: null,
            'midtrans_raw'            => json_encode($request->all()),
        ])->save();

        Log::info('midtrans.webhook.saved', [
            'order_id' => $order->id,
            'payment_status' => $order->payment_status,
            'midtrans_status'=> $order->midtrans_status,
        ]);

        // Penting: jawab 200 agar Midtrans tidak retry berulang
        return response('OK', 200);
    }

    // Redirect handlers dari Snap (opsional)
    public function finish(Request $r)   { /* tampilkan thank you */ }
    public function unfinish(Request $r) { /* tampilkan pending */ }
    public function error(Request $r)    { /* tampilkan error */ }
}
