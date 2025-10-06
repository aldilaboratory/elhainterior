<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class AllProductsController extends Controller
{
    public function index(Request $r)
    {
        $q      = trim($r->get('q', ''));
        $cat    = $r->integer('category');
        $sort   = $r->get('sort', 'latest'); // latest|price_asc|price_desc|name

        $products = Product::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($cat, fn ($qq) => $qq->where('category_id', $cat))
            ->when($sort, function ($qq) use ($sort) {
                return match ($sort) {
                    'price_asc'  => $qq->orderBy('price', 'asc'),
                    'price_desc' => $qq->orderBy('price', 'desc'),
                    'name'       => $qq->orderBy('name', 'asc'),
                    default      => $qq->latest(), // latest
                };
            })
            ->paginate(12)
            ->withQueryString();

        // kategori untuk sidebar (kalau belum dipush via View Composer)
        $categories = Category::orderBy('name')->get(['id','name']);

        return view('customer.all-products', [
            'products'   => $products,
            'categories' => $categories,
            'q'          => $q,
            'cat'        => $cat,
            'sort'       => $sort,
        ]);
    }
}
