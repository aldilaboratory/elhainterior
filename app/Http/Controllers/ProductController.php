<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Menampilkan daftar produk dengan filter pencarian dan kategori.
     */
    public function index(Request $request)
    {
        $searchKeyword = $request->input('search');
        $selectedCategoryId = $request->input('category_id');

        $products = Product::with(['category','subcategory'])
            ->when($searchKeyword, function ($query) use ($searchKeyword) {
                $query->where(function ($q) use ($searchKeyword) {
                    $q->where('name','like',"%{$searchKeyword}%")
                      ->orWhere('description','like',"%{$searchKeyword}%");
                });
            })
            ->when($selectedCategoryId, fn($q) => $q->where('category_id', $selectedCategoryId))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $categories    = Category::orderBy('name')->get();
        $subcategories = Subcategory::orderBy('name')->get();

        return view('admin.products.index', compact(
            'products','categories','subcategories','searchKeyword','selectedCategoryId'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories    = Category::orderBy('name')->get();
        $subcategories = Subcategory::orderBy('name')->get();

        return view('admin.products.create', compact('categories','subcategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Menyimpan produk baru.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if (!str_starts_with($file->getMimeType() ?? '', 'image/')) {
                return back()->withErrors('File yang diupload harus gambar.')->withInput();
            }
            $validated['image_path'] = $file->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success','Produk berhasil ditambahkan.');
    }

    /** Opsional: helper untuk pesan error upload */
    private function uploadErrorText(?int $code): string
    {
        return match ($code) {
            \UPLOAD_ERR_INI_SIZE   => 'Ukuran file melebihi upload_max_filesize.',
            \UPLOAD_ERR_FORM_SIZE  => 'Ukuran file melebihi batas form.',
            \UPLOAD_ERR_PARTIAL    => 'File terupload sebagian.',
            \UPLOAD_ERR_NO_FILE    => 'Tidak ada file yang diunggah.',
            \UPLOAD_ERR_NO_TMP_DIR => 'Folder tmp tidak ditemukan.',
            \UPLOAD_ERR_CANT_WRITE => 'Gagal menulis ke disk.',
            \UPLOAD_ERR_EXTENSION  => 'Upload dibatalkan ekstensi PHP.',
            default                => 'File tidak valid atau tmp path kosong.',
        };
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
        $product       = Product::findOrFail($id);
        $categories    = Category::orderBy('name')->get();
        $subcategories = Subcategory::orderBy('name')->get();

        return view('admin.products.edit', compact('product','categories','subcategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Memperbarui data produk.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product   = Product::findOrFail($id);
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);   // <-- tambahkan ini

        if ($request->hasFile('image')) {
            if ($product->image_path && \Storage::disk('public')->exists($product->image_path)) {
                \Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products','public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success','Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Menghapus produk.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
