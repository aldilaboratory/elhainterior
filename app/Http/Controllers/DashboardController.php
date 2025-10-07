<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        // Pesanan yang sudah bayar tapi belum dikonfirmasi
        $unconfirmed = Order::withCount('items')
            ->where('payment_status', 'paid')
            ->where('order_status', 'unconfirmed')
            ->latest('id')
            ->take(10)
            ->get();

        $unconfirmedCount = Order::where('payment_status', 'paid')
            ->where('order_status', 'unconfirmed')
            ->count();

        // Order baru = order_status 'unconfirmed'
        $newOrders  = Order::where('order_status', 'unconfirmed')->count();

        // Total penjualan = jumlah total dari order yang sudah dibayar
        $salesTotal = Order::where('payment_status', 'paid')->sum('total');

        // Total pengguna
        $userCount  = User::count();

        return view('admin.dashboard', compact('unconfirmed', 'unconfirmedCount', 'newOrders','salesTotal','userCount'));
    }
}
