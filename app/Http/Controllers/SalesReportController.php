<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        $orders = Order::query()
            ->where('payment_status', 'paid') // hanya lunas
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue = $orders->sum('total');

        return view('admin.sales-report.index', compact('orders', 'from', 'to', 'totalRevenue'));
    }

    public function exportPdf(Request $request)
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        $orders = Order::query()
            ->where('payment_status', 'paid')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue = $orders->sum('total');

        $pdf = Pdf::loadView('admin.sales-report.pdf', compact('orders', 'from', 'to', 'totalRevenue'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-penjualan-lunas.pdf');
    }
}
