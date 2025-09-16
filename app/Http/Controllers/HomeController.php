<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // slider/“paling dicari” bisa pakai produk terbaru atau pakai kolom populer jika ada
        $popularProducts = Product::with(['category'])
            ->latest()
            ->take(10)
            ->get();

        // kategori untuk tabs + ambil N produk per kategori
        $categories = Category::with(['products' => function ($q) {
                $q->latest()->take(8); // jumlah kartu per tab
            }])
            ->orderBy('name')
            ->get();

        return view('customer.home', compact('popularProducts','categories'));
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
    public function show(string $id)
    {
        //
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
