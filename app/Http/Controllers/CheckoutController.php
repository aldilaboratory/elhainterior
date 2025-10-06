<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    /**
     * Halaman checkout.
     */
    public function index(Request $request)
    {
        $cart = $request->user()
            ->cart()
            ->with(['items.product'])
            ->where('is_active', true)
            ->firstOrFail();

        $lines = $cart->items;

        // Hitung subtotal & total berat (gram)
        $subtotal = 0;
        $totalWeightGram = 0;
        foreach ($lines as $item) {
            $subtotal += $item->qty * $item->unit_price;
            $w = (int) ($item->product->weight ?? 0);
            $totalWeightGram += $w * (int) $item->qty;
        }

        $shipping = 0;
        $total    = $subtotal + $shipping;

        $user = $request->user();
        $addresses = $user->addresses()->latest()->get();
        $defaultAddressId = optional(
            $user->addresses()->where('is_default', true)->first()
        )->id;

        return view('customer.checkout.index', compact(
            'lines', 'subtotal', 'shipping', 'total',
            'addresses', 'defaultAddressId', 'totalWeightGram'
        ));
    }

    /**
     * Submit checkout → buat order → buat transaksi Snap → redirect ke Midtrans.
     */
    public function store(Request $request)
    {
        // === Validasi alamat
        $mode = $request->input('addr_mode'); // 'saved' | 'new'

        if ($mode === 'saved') {
            $request->validate([
                'shipping_address_id' => 'required|integer|exists:addresses,id',
            ]);
        } else {
            $request->validate([
                'na_label'             => 'required|string|max:50',
                'na_recipient'         => 'required|string|max:255',
                'na_phone'             => 'required|string|max:30',
                'na_address'           => 'required|string|max:255',
                'na_destination_id'    => 'required|integer|min:1',
                'na_destination_label' => 'required|string|max:255',
                'na_postal_code'       => 'nullable|string|max:20',
                'na_is_default'        => 'nullable|boolean',
            ]);
        }

        // === Wajib pilih layanan ongkir
        $request->validate([
            'courier_code'    => 'required|string',
            'courier_service' => 'required|string',
            'shipping'        => 'required|integer|min:0',
            'shipping_etd'    => 'nullable|string',
        ]);

        $user = $request->user();

        // === Ambil keranjang & hitung ulang angka penting (jangan percaya angka dari client)
        $cart = $user->cart()->with('items.product')->where('is_active', true)->firstOrFail();
        if ($cart->items->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang kosong.'])->withInput();
        }

        $subtotal = 0;
        $weight   = 0;
        foreach ($cart->items as $ci) {
            $subtotal += $ci->qty * $ci->unit_price;
            $weight   += max(0, (int) ($ci->product->weight ?? 0)) * (int) $ci->qty;
        }
        $weight   = max(1, $weight);
        $shipping = (int) $request->input('shipping');
        $total    = $subtotal + $shipping;

        // === Siapkan order_code unik
        $orderCode = 'INV-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        // === Buat Address (jika NEW) + Order + OrderItem, serta kosongkan cart
        $address = null;

        DB::transaction(function () use ($request, $user, $mode, &$address, $subtotal, $weight, $shipping, $total, $cart, $orderCode) {
            if ($mode === 'new') {
                $makeDefault = $request->boolean('na_is_default');
                if ($makeDefault) {
                    Address::where('user_id', $user->id)->update(['is_default' => false]);
                }
                $address = Address::create([
                    'user_id'           => $user->id,
                    'label'             => (string) $request->string('na_label'),
                    'recipient_name'    => (string) $request->string('na_recipient'),
                    'phone'             => (string) $request->string('na_phone'),
                    'address_line'      => (string) $request->string('na_address'),
                    'destination_id'    => (int) $request->integer('na_destination_id'),
                    'destination_label' => (string) $request->string('na_destination_label'),
                    'postal_code'       => (string) $request->input('na_postal_code', ''), // <-- isi dari autocomplete
                    'is_default'        => $makeDefault,
                ]);
            } else {
                $address = Address::where('id', $request->integer('shipping_address_id'))
                    ->where('user_id', $user->id)->firstOrFail();
            }

            /** @var \App\Models\Order $order */
            $order = Order::create([
                'user_id'      => $user->id,
                'order_code'   => $orderCode,
                'first_name'   => $user->name,
                'last_name'    => null,
                'email'        => $user->email,
                'phone'        => $address->phone ?: $user->phone,
                'address1'     => $address->address_line,
                'postal_code'  => $address->postal_code ?: (string) $request->input('na_postal_code', ''), // <-- fallback
                'destination_id'       => $address->destination_id,
                'ship_to_region_label' => $address->destination_label,
                'subtotal'     => $subtotal,
                'weight_total_gram'    => $weight,
                'shipping'     => $shipping,
                'courier_code' => (string) $request->string('courier_code'),
                'courier_service' => (string) $request->string('courier_service'),
                'shipping_etd'  => (string) $request->string('shipping_etd'),
                'total'        => $total,
                'payment_status' => 'pending',
                'order_status'   => 'unconfirmed',
            ]);

            foreach ($cart->items as $ci) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $ci->product_id,
                    'name'       => $ci->product->name,
                    'price'      => $ci->unit_price,
                    'qty'        => $ci->qty,
                    'line_total' => $ci->qty * $ci->unit_price,
                ]);

                // (opsional) kurangi stok
                if (method_exists($ci->product, 'decrement')) {
                    $ci->product->decrement('stock', (int) $ci->qty);
                }
            }

            // kosongkan keranjang
            $cart->items()->delete();
            $cart->update(['is_active' => false]);
        });

        // === Build item_details untuk transparansi (opsional)
        $itemDetails = [];
        foreach ($cart->items as $ci) {
            $itemDetails[] = [
                'id'       => (string) $ci->product_id,
                'price'    => (int) $ci->unit_price,
                'quantity' => (int) $ci->qty,
                'name'     => Str::limit($ci->product->name, 50, ''),
            ];
        }
        if ($shipping > 0) {
            $itemDetails[] = [
                'id'       => 'SHIPPING',
                'price'    => (int) $shipping,
                'quantity' => 1,
                'name'     => 'Ongkir',
            ];
        }

        // === Siapkan parameter Snap
        $params = [
            'transaction_details' => [
                'order_id'     => $orderCode,
                'gross_amount' => (int) $total, // W A J I B integer
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $address->phone ?: $user->phone,
                'shipping_address' => [
                    'first_name'  => $user->name,
                    'phone'       => $address->phone ?: $user->phone,
                    'address'     => $address->address_line,
                    'postal_code' => $address->postal_code,
                ],
            ],
            'item_details' => $itemDetails,
            'callbacks' => [
                'finish'   => route('midtrans.finish',   ['order' => $orderCode]),
                'unfinish' => route('midtrans.unfinish', ['order' => $orderCode]),
                'error'    => route('midtrans.error',    ['order' => $orderCode]),
            ],
            // 'expiry' => ['unit' => 'minutes', 'duration' => 120], // opsional
        ];

        // === Buat transaksi Snap dan redirect user ke halaman pembayaran
        $trx = Snap::createTransaction($params);

        // simpan info awal ke order
        Order::where('order_code', $orderCode)->update([
            'midtrans_order_id'       => $orderCode,
            'midtrans_raw'            => json_encode(['redirect_url' => $trx->redirect_url, 'token' => $trx->token]),
            'midtrans_payment_type'   => null,
            'midtrans_transaction_id' => null,
            'payment_status'          => 'pending',
        ]);

        return redirect()->away($trx->redirect_url);
    }

    public function pay(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        // kalau SUDAH ada token, langsung pakai ulang
        if ($order->snap_token && $order->midtrans_redirect_url) {
            return redirect()->away($order->midtrans_redirect_url);
        }

        // kalau BELUM ada token → buat sekali saja
        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_code,       // tetap kode kamu
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->first_name,
                'email'      => $order->email,
                'phone'      => $order->phone,
                'shipping_address' => [
                    'first_name'  => $order->first_name,
                    'phone'       => $order->phone,
                    'address'     => $order->address1,
                    'postal_code' => $order->postal_code,
                ],
            ],
            'item_details' => $order->items->map(function ($it) {
                return [
                    'id'       => (string) $it->product_id,
                    'price'    => (int) $it->price,
                    'quantity' => (int) $it->qty,
                    'name'     => \Illuminate\Support\Str::limit($it->name, 50, ''),
                ];
            })->values()->all(),
            'callbacks' => [
                'finish'   => route('midtrans.finish',   ['order' => $order->order_code]),
                'unfinish' => route('midtrans.unfinish', ['order' => $order->order_code]),
                'error'    => route('midtrans.error',    ['order' => $order->order_code]),
            ],
        ];

        $trx = Snap::createTransaction($params);

        $order->update([
            'midtrans_order_id'       => $order->order_code,     // simpan id yg dipakai Midtrans
            'snap_token'              => $trx->token,
            'midtrans_redirect_url'   => $trx->redirect_url,
            'payment_status'          => 'pending',
        ]);

        return redirect()->away($trx->redirect_url);
    }
}
