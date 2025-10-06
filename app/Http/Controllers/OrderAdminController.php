<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    // mapping label → query
    private const TABS = [
        'all'              => 'Semua',
        'waiting_payment'  => 'Menunggu Pembayaran',
        'unconfirmed'      => 'Belum Dikonfirmasi',
        'packing'          => 'Sedang Packing',
        'shipping'         => 'Dalam Pengiriman',
        'completed'        => 'Pesanan Selesai',
        'cancelled'        => 'Pesanan Dibatalkan',
        'rejected'         => 'Pesanan Ditolak',
    ];

    // untuk badge pewarnaan
    private const PAY_BADGE = [
        'pending'  => 'warning',
        'paid'     => 'success',
        'failed'   => 'danger',
        'refunded' => 'secondary',
    ];

    private const ORDER_BADGE = [
        'unconfirmed' => 'secondary',
        'packing'     => 'info text-dark',
        'shipped'     => 'primary',
        'completed'   => 'success',
        'cancelled'   => 'dark',
        'rejected'    => 'danger',
    ];

    public function index(Request $r)
    {
        $tab    = $r->string('tab', 'all');
        $search = trim((string) $r->input('search', ''));

        $q = Order::query()
            ->with('user')        // pastikan relasi user ada
            ->withCount('items')  // qty item per order
            ->latest('id');

        // filter tab
        switch ($tab) {
            case 'waiting_payment':
                $q->where('payment_status', 'pending');
                break;
            case 'unconfirmed':
                $q->where('payment_status', 'paid')
                  ->where('order_status', 'unconfirmed');
                break;
            case 'packing':
                $q->where('order_status', 'packing');
                break;
            case 'shipping':
                $q->where('order_status', 'shipped');
                break;
            case 'completed':
                $q->where('order_status', 'completed');
                break;
            case 'cancelled':
                $q->where('order_status', 'cancelled');
                break;
            case 'rejected':
                $q->where('order_status', 'rejected');
                break;
            case 'all':
            default:
                // no-op
                break;
        }

        // cari
        if ($search !== '') {
            $q->where(function ($x) use ($search) {
                $x->where('order_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $orders = $q->paginate(20)->withQueryString();

        // hitung untuk badge jumlah di tab
        $stats = [
            'all'              => Order::count(),
            'waiting_payment'  => Order::where('payment_status', 'pending')->count(),
            'unconfirmed'      => Order::where('payment_status','paid')->where('order_status','unconfirmed')->count(),
            'packing'          => Order::where('order_status','packing')->count(),
            'shipping'         => Order::where('order_status','shipped')->count(),
            'completed'        => Order::where('order_status','completed')->count(),
            'cancelled'        => Order::where('order_status','cancelled')->count(),
            'rejected'         => Order::where('order_status','rejected')->count(),
        ];

        return view('admin.orders.index', [
            'orders'     => $orders,
            'tab'        => $tab,
            'search'     => $search,
            'tabs'       => self::TABS,
            'stats'      => $stats,
            'payBadge'   => self::PAY_BADGE,
            'orderBadge' => self::ORDER_BADGE,
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items.product','user']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $r, Order $order)
    {
        $data = $r->validate([
            'order_status'     => 'required|in:unconfirmed,packing,shipped,completed,cancelled,rejected',
            'tracking_code'    => 'nullable|string|max:64',
            'shipping_courier' => 'nullable|string|max:32',
        ]);

        $order->fill($data)->save();

        // opsional: kalau tandai completed otomatis set completed_at
        if ($order->wasChanged('order_status') && $order->order_status === 'completed') {
            $order->update(['completed_at' => now()]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
