<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        ini_set('upload_tmp_dir', storage_path('tmp'));

        // Kategori untuk header (aman untuk guest)
        View::composer(['layouts.*','partials.*','customer.*'], function ($view) {
            $headerCategories = Cache::remember('header.categories', 600, function () {
                return Category::with(['subcategories' => fn($q) => $q->orderBy('name')])
                    ->orderBy('name')
                    ->get();
            });

            $view->with('headerCategories', $headerCategories);
        });

        // Mini cart di header
        View::composer(['partials.header-cart'], function ($view) {
            $lines = collect();
            $total = 0;
            $placeholder = 'https://placehold.co/70x70';

            if (auth()->check()) {
                $cart = Cart::with([
                        'items.product' => function ($q) {
                            $q->select('id','name','slug','price','image_path')
                              ->with('primaryImage'); // <<-- eager load thumbnail
                        }
                    ])
                    ->where('user_id', auth()->id())
                    ->where('is_active', true)
                    ->first();

                if ($cart) {
                    $lines = $cart->items->map(function ($it) use ($placeholder) {
                        $p = $it->product;
                        return (object)[
                            'item_id'    => $it->id,
                            'name'       => $p->name,
                            'slug'       => $p->slug,
                            'image_url'  => $p->thumbnail_url ?? $placeholder,
                            'qty'        => (int) $it->qty,
                            'unit_price' => (int) $it->unit_price,
                            'line_total' => (int) $it->qty * (int) $it->unit_price,
                        ];
                    });
                    $total = (int) $lines->sum('line_total');
                }
            } else {
                // Guest: pakai pending_cart dari session
                $pending = collect(session('pending_cart', []));
                if ($pending->isNotEmpty()) {
                    $byId = $pending->groupBy('product_id')
                                    ->map(fn($g)=>collect($g)->sum('qty'));

                    $products = Product::with('primaryImage')
                        ->whereIn('id', $byId->keys())
                        ->get(['id','name','slug','price','image_path']);

                    $lines = $products->map(function ($p) use ($byId, $placeholder) {
                        $qty  = (int) $byId[$p->id];
                        $unit = (int) $p->price;
                        return (object)[
                            'item_id'    => null,
                            'name'       => $p->name,
                            'slug'       => $p->slug,
                            'image_url'  => $p->thumbnail_url ?? $placeholder,
                            'qty'        => $qty,
                            'unit_price' => $unit,
                            'line_total' => $qty * $unit,
                        ];
                    });
                    $total = (int) $lines->sum('line_total');
                }
            }

            $view->with('miniCart', (object)[
                'count' => (int) $lines->sum('qty'),
                'total' => $total,
                'lines' => $lines,
            ]);
        });
    }
}