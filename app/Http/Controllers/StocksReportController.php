<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StocksReportController extends Controller
{
    /**
     * Laporan stok:
     * - stock_on_hand (kolom: products.stock)
     * - terjual 30 hari terakhir
     * - terjual total (selesai/terkirim)
     * - nilai stok (stock * price)
     * Filter: q (nama/sku), category_id, days (default 30)
     * Export CSV: ?export=csv
     */
    public function index(Request $r)
    {
        $days        = (int) $r->integer('days', 30);
        $q           = trim($r->get('q', ''));
        $categoryId  = $r->integer('category_id');
        $lowStockMin = (int) $r->integer('low_min', 5); // ambang “low stock”

        // Status order yang dihitung sebagai penjualan selesai
        $doneStatuses = ['shipped', 'completed']; // sesuaikan dg skema kamu

        $products = Product::query()
            ->with(['category'])
            // total sold (selesai)
            ->withSum(['orderItems as sold_total' => function ($q2) use ($doneStatuses) {
                $q2->whereHas('order', function ($oo) use ($doneStatuses) {
                    $oo->whereIn('order_status', $doneStatuses);
                });
            }], 'qty')
            // sold N days
            ->withSum(['orderItems as sold_ndays' => function ($q2) use ($days, $doneStatuses) {
                $q2->where('created_at', '>=', now()->subDays($days))
                   ->whereHas('order', function ($oo) use ($doneStatuses) {
                       $oo->whereIn('order_status', $doneStatuses);
                   });
            }], 'qty')
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->when($categoryId, fn ($qq) => $qq->where('category_id', $categoryId))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        // Statistik ringkas header
        $summary = [
            'items'        => (int) $products->total(),
            'stock_on_hand'=> (int) $products->sum('stock'),
            'stock_value'  => (int) $products->sum(fn($p)=> (int)$p->stock * (int)($p->price ?? 0)),
        ];

        // Export CSV?
        if ($r->get('export') === 'csv') {
            return $this->exportCsv($products->getCollection(), $days);
        }

        return view('admin.stocks-report.index', [
            'products'   => $products,
            'days'       => $days,
            'q'          => $q,
            'categoryId' => $categoryId,
            'lowStockMin'=> $lowStockMin,
            'summary'    => $summary,
        ]);
    }

    private function exportCsv($rows, int $days): StreamedResponse
    {
        $filename = 'stocks-report-'.now()->format('Ymd_His').".csv";

        return response()->streamDownload(function () use ($rows, $days) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Nama', 'SKU', 'Harga', 'Stok', "Terjual {$days}d", 'Terjual Total', 'Nilai Stok']);

            foreach ($rows as $p) {
                $price     = (int) ($p->price ?? 0);
                $stock     = (int) ($p->stock ?? 0);
                $soldN     = (int) ($p->sold_ndays ?? 0);
                $soldTotal = (int) ($p->sold_total ?? 0);

                fputcsv($out, [
                    $p->name,
                    $p->sku,
                    $price,
                    $stock,
                    $soldN,
                    $soldTotal,
                    $price * $stock,
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
