@extends('layouts.admin-layout')

@section('content')
  <section class="section">
    <div class="container-fluid">

      <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="title">
                <h2>Kelola Produk</h2>
              </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
              <div class="breadcrumb-wrapper">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                      <a href="#0">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                      Kelola Produk
                    </li>
                  </ol>
                </nav>
              </div>
            </div>
            <!-- end col -->
          </div>
          <!-- end row -->
        </div>
      <!-- ========== title-wrapper end ========== -->
      
      <!-- Header actions -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2">
          <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ Tambah produk baru</a>
        </div>
      </div>

      {{-- Filters --}}
      <form method="GET" class="row g-2 mb-3">
        <div class="col-lg-6">
          <input type="text" name="search" value="{{ $searchKeyword }}"
                class="form-control" placeholder="Cari produk">
        </div>
        <div class="col-lg-6">
          <select name="category_id" class="form-select" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}"
                {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
              </option>
            @endforeach
          </select>
        </div>
      </form>

      {{-- Table --}}
      <div class="card shadow-sm pb-3">
        <div class="card-body p-0">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-center px-2" style="width:40px;">#</th>
                <th class="px-2">Nama Produk</th>
                <th class="px-2">Deskripsi</th>
                <th class="px-2">Kategori</th>
                <th class="px-2">Sub Kategori</th>
                <th class="px-2">Harga</th>
                <th class="px-2">Stok</th>
                <th class="px-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($products as $productIndex => $product)
                <tr>
                  <td class="text-center px-2">{{ $products->firstItem() + $productIndex }}</td>
                  <td class="px-2">
                    <div class="d-flex align-items-center gap-3">
                      <img src="{{ $product->image_url ?? 'https://placehold.co/80x80' }}"
                          alt="Gambar {{ $product->name }}"
                          class="rounded" style="width:80px;height:80px;object-fit:cover;">
                      <span class="fw-semibold">{{ $product->name }}</span>
                    </div>
                  </td>
                  <td class="px-2 text-truncate" style="max-width:240px;">
                    {{ Str::limit($product->description, 80) }}
                  </td>
                  <td class="px-2">{{ $product->category->name ?? '-' }}</td>
                  <td class="px-2">{{ $product->subcategory->name ?? '-' }}</td>
                  <td class="px-2">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                  <td class="px-2 text-center">{{ $product->stock }}</td>
                  <td class="px-2">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-info btn-sm text-white">
                      <i class="mdi mdi-pencil"></i>
                    </a>

                    <form id="delete-form-{{ $product->id }}" action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button class="btn btn-danger btn-sm text-white delete-btn" data-id="{{ $product->id }}" data-name="Produk {{ $product->name }}">
                        <i class="mdi mdi-delete"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="text-center">Belum ada produk</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-3">
        {{ $products->links() }}
      </div>

    </div>
  </section>
@endsection