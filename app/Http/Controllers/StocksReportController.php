<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // ⬅️ pakai DomPDF untuk PDF

class StocksReportController extends Controller
{
    /**
     * Laporan stok ringkas:
     * - stock_on_hand (products.stock)
     * - terjual total (order_status: shipped/completed)
     * - nilai stok (stock * price)
     * Filter: q (nama), category_id
     * Download PDF: route admin.stocks-report.pdf
     */
    public function index(Request $r)
    {
        $q          = trim((string) $r->get('q', ''));
        $categoryId = $r->integer('category_id');

        $doneStatuses = ['shipped', 'completed'];

        $products = Product::query()
            ->with(['category'])
            ->withSum(['orderItems as sold_total' => function ($q2) use ($doneStatuses) {
                $q2->whereHas('order', function ($oo) use ($doneStatuses) {
                    $oo->whereIn('order_status', $doneStatuses);
                });
            }], 'qty')
            ->when($q, function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%"); // ⬅️ hanya nama
            })
            ->when($categoryId, fn ($qq) => $qq->where('category_id', $categoryId))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $summary = [
            'items'         => (int) $products->total(),
            'stock_on_hand' => (int) $products->sum('stock'),
            'stock_value'   => (int) $products->sum(fn($p)=> (int)$p->stock * (int)($p->price ?? 0)),
        ];

        return view('admin.stocks-report.index', [
            'products'   => $products,
            'q'          => $q,
            'categoryId' => $categoryId,
            'summary'    => $summary,
        ]);
    }

    public function downloadPdf(Request $r)
    {
        $q          = trim((string) $r->get('q', ''));
        $categoryId = $r->integer('category_id');

        $doneStatuses = ['shipped', 'completed'];

        // Ambil semua (tanpa paginate) untuk PDF
        $rows = Product::query()
            ->with(['category'])
            ->withSum(['orderItems as sold_total' => function ($q2) use ($doneStatuses) {
                $q2->whereHas('order', function ($oo) use ($doneStatuses) {
                    $oo->whereIn('order_status', $doneStatuses);
                });
            }], 'qty')
            ->when($q, fn ($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->when($categoryId, fn ($qq) => $qq->where('category_id', $categoryId))
            ->orderBy('name')
            ->get();

        $summary = [
            'items'         => (int) $rows->count(),
            'stock_on_hand' => (int) $rows->sum('stock'),
            'stock_value'   => (int) $rows->sum(fn($p)=> (int)$p->stock * (int)($p->price ?? 0)),
        ];

        $pdf = Pdf::loadView('admin.stocks-report.pdf', [
            'rows'     => $rows,
            'summary'  => $summary,
            'q'        => $q,
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-stok-'.now()->format('Ymd_His').'.pdf';
        return $pdf->download($filename);
    }
}
