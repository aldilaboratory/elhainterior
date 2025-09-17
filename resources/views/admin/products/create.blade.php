@extends('layouts.admin-layout')

@section('content')
<section class="section">
  <div class="container-fluid">
    <div class="title-wrapper pt-30 mb-3">
      <h2>Tambah Produk</h2>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="card p-3">
      @csrf

      <div class="row g-3">
        <div class="col-lg-6">
          <label class="form-label">Nama</label>
          <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>

        <div class="col-lg-3">
          <label class="form-label">Harga (Rp)</label>
          <input type="number" name="price" value="{{ old('price') }}" min="0" class="form-control" required>
        </div>

        <div class="col-lg-3">
          <label class="form-label">Stok</label>
          <input type="number" name="stock" value="{{ old('stock') }}" min="0" class="form-control" placeholder="0" required>
        </div>

        <div class="col-lg-3">
          <label class="form-label">Berat (gram)</label>
          <input type="number" name="weight" value="{{ old('weight') }}" min="0" class="form-control" placeholder="0" required>
        </div>

        <div class="col-lg-6">
          <label class="form-label">Kategori</label>
          <select name="category_id" id="categorySelect" class="form-select" required>
            <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Pilih kategori</option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
          <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div class="col-12">
          <label class="form-label">Gambar Produk (bisa lebih dari satu)</label>
          <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
          <div class="form-text">Pilih beberapa file. Gambar pertama otomatis jadi utama, atau kirim primary_index.</div>
        </div>
      </div>

      <div class="mt-3 text-end">
        <a href="{{ route('admin.products.index') }}" class="btn btn-light mx-1">Batal</a>
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div>
</section>

{{-- filter subcategory by category --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const allSubcategories = @json($subcategories);
    const categorySelect    = document.getElementById('categorySelect');
    const subcategorySelect = document.getElementById('subcategorySelect');

    function refillSubcategories() {
      const selectedCategoryId = categorySelect.value;
      const oldSelected = "{{ old('subcategory_id') }}";

      subcategorySelect.innerHTML = '<option value="">Pilih subkategori</option>';

      allSubcategories
        .filter(s => String(s.category_id) === String(selectedCategoryId))
        .forEach(s => {
          const option = document.createElement('option');
          option.value = s.id;
          option.textContent = s.name;
          if (oldSelected && String(oldSelected) === String(s.id)) option.selected = true;
          subcategorySelect.appendChild(option);
        });
    }

    categorySelect.addEventListener('change', refillSubcategories);
    if (categorySelect.value) refillSubcategories(); // initial fill
  });
</script>
@endsection
