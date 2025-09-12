<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        DB::transaction(function () use ($request, $data) {
            /** @var \App\Models\Product $product */
            $product = Product::create(collect($data)->except(['images','primary_index'])->all());

            $files = $request->file('images', []); // array atau []
            $primaryIndex = (int) $request->input('primary_index', 0);

            foreach ($files as $i => $file) {
                /** @var UploadedFile $file */
                if (!$file->isValid()) continue;

                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'is_primary' => $i === $primaryIndex,
                    'sort_order' => $i,
                ]);
            }

            // fallback: jika tidak ada yang ditandai utama tetapi ada gambar, set pertama
            if ($product->images()->exists() && !$product->primaryImage()->exists()) {
                $first = $product->images()->orderBy('sort_order')->first();
                $first->update(['is_primary' => true]);
            }
        });

        return redirect()->route('admin.products.index')->with('success','Produk berhasil ditambahkan.');
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
        $product = Product::findOrFail($id);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        DB::transaction(function () use ($request, $product, $data) {
            $product->update(collect($data)->except(['images','delete_images','set_primary'])->all());

            // hapus gambar terpilih
            $toDelete = $request->input('delete_images', []);
            if (!empty($toDelete)) {
                $imgs = $product->images()->whereIn('id', $toDelete)->get();
                foreach ($imgs as $img) {
                    \Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }

            // tambah gambar baru
            $files = $request->file('images', []);
            $startOrder = (int) $product->images()->max('sort_order') + 1;
            foreach ($files as $offset => $file) {
                if (!$file->isValid()) continue;
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'is_primary' => false,
                    'sort_order' => $startOrder + $offset,
                ]);
            }

            // set primary dari radio
            if ($pid = $request->input('set_primary')) {
                $product->images()->update(['is_primary' => false]);
                $product->images()->where('id', $pid)->update(['is_primary' => true]);
            }

            // jaga-jaga: kalau semua terhapus, tidak ada primary
            if (!$product->images()->exists()) {
                // nothing, biarkan tanpa gambar
            } elseif (!$product->primaryImage()->exists()) {
                $product->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
            }
        });

        return redirect()->route('admin.products.index')->with('success','Produk berhasil diperbarui.');
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
