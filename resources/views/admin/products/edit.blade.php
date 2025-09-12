@extends('layouts.admin-layout')

@section('content')
<section class="section">
  <div class="container-fluid">
    <div class="title-wrapper pt-30 mb-3">
      <h2>Edit Produk</h2>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="card p-3">
      @csrf @method('PUT')

      <div class="row g-3">
        <div class="col-lg-6">
          <label class="form-label">Nama</label>
          <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" required>
        </div>

        <div class="col-lg-3">
          <label class="form-label">Harga (Rp)</label>
          <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" class="form-control" required>
        </div>

        <div class="col-lg-3">
          <label class="form-label">Stok</label>
          <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" class="form-control" required>
        </div>

        <div class="col-lg-6">
          <label class="form-label">Kategori</label>
          <select name="category_id" id="categorySelect" class="form-select" required>
            <option value="" disabled>Pilih kategori</option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-lg-6">
          <label class="form-label">Sub Kategori (opsional)</label>
          <select name="subcategory_id" id="subcategorySelect" class="form-select">
            <option value="">Pilih subkategori</option>
            {{-- opsi akan diisi via JS --}}
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="col-12">
          @if(isset($product))
            <div class="row g-2">
            <label class="form-label">Gambar</label>
              @foreach($product->images as $img)
                <div class="col-6 col-md-3">
                  <div class="img-container" style="max-height: 200px; object-fit: cover; overflow: hidden;">
                    <img src="{{ $img->url }}" class="img-fluid  rounded mb-1" alt="">
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="set_primary" value="{{ $img->id }}" {{ $img->is_primary ? 'checked' : '' }}>
                    <label class="form-check-label">Jadikan utama</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $img->id }}">
                    <label class="form-check-label text-danger">Hapus</label>
                  </div>
                </div>
              @endforeach
            </div>
            <label class="form-label mt-3">Tambah Gambar Baru (opsional)</label>
            <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
          @endif
        </div>
      </div>

      <div class="mt-3 text-end">
        <a href="{{ route('admin.products.index') }}" class="btn btn-light mx-1">Batal</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const allSubcategories = @json($subcategories);
    const categorySelect    = document.getElementById('categorySelect');
    const subcategorySelect = document.getElementById('subcategorySelect');

    function refillSubcategories() {
      const selectedCategoryId = categorySelect.value;
      const selectedSubId = "{{ old('subcategory_id', $product->subcategory_id) }}";

      subcategorySelect.innerHTML = '<option value="">Pilih subkategori</option>';
      allSubcategories
        .filter(s => String(s.category_id) === String(selectedCategoryId))
        .forEach(s => {
          const option = document.createElement('option');
          option.value = s.id;
          option.textContent = s.name;
          if (selectedSubId && String(selectedSubId) === String(s.id)) option.selected = true;
          subcategorySelect.appendChild(option);
        });
    }

    categorySelect.addEventListener('change', refillSubcategories);
    refillSubcategories(); // initial
  });
</script>
@endsection
