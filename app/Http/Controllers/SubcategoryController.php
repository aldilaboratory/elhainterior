<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubcategoryRequest;
use App\Http\Requests\UpdateSubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
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
    public function store(StoreSubcategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        Subcategory::create($data);
        return back()->with('success','Subkategori berhasil ditambahkan.');
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
    public function edit($id)
    {
        $sub   = Subcategory::findOrFail($id);
        $cats  = Category::orderBy('name')->get();
        return view('admin.subcategories.edit', compact('sub','cats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubcategoryRequest $request, $id)
    {
        $sub = Subcategory::findOrFail($id);
        $sub->update($request->validated());
        return to_route('admin.categories.index')->with('success','Subkategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sub = Subcategory::findOrFail($id);

        try {
            $sub->delete();
            return back()->with('success', 'Subkategori dihapus.');
        } catch (QueryException $e) {
            // Jika ada foreign key (mis. products.subcategory_id RESTRICT)
            return back()->with('error', 'Subkategori tidak bisa dihapus karena masih dipakai produk.');
        }
    }
}
