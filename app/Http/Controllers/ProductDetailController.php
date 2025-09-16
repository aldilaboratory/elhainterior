<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // kalau sudah punya tabel product_images, eager load
        $product->load(['category','subcategory','images' => fn($q) => $q->orderBy('sort_order')]);

        // Related products (kategori sama, bukan dirinya sendiri)
        $related = Product::where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->latest()
            ->take(12)
            ->get();

        // Buat gallery fallback: kalau tidak ada images, pakai image_path utama
        $gallery = $product->relationLoaded('images') && $product->images->isNotEmpty()
            ? $product->images->map(fn($img) => asset('storage/'.$img->path))
            : collect([$product->image_url])->filter();

        return view('customer.product-details', compact('product','related','gallery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
