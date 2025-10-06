<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        // Filter tanggal dari query string
        $from = $request->query('from');
        $to   = $request->query('to');
        $status = $request->query('status', 'all');

        $orders = Order::query()
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->when($status !== 'all', fn($q) => $q->where('payment_status', $status))
            ->orderByDesc('created_at')
            ->get();

        // Hitung total penjualan
        $totalRevenue = $orders->sum('total');

        return view('admin.sales-report.index', [
            'orders' => $orders,
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'totalRevenue' => $totalRevenue,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $orders = Order::where('payment_status','paid')->get();
        $pdf = Pdf::loadView('admin.sales-report.pdf', compact('orders'));
        return $pdf->download('laporan-penjualan.pdf');
    }
}
