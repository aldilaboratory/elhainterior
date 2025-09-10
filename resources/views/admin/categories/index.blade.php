@extends('layouts.admin-layout')

@section('content')
  <section class="section">
    <div class="container-fluid">

      <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="title">
                <h2>Kelola Kategori</h2>
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
                      Kategori
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
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateCategory">+ Tambah kategori baru</button>
        </div>
      </div>

      <!-- Filters -->
      <div class="row g-2 mb-3">
        <div class="col-12 col-lg-12">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Cari kategori...">
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th class="text-center px-2">#</th>
                <th class="px-2">Nama Kategori</th>
                <th class="px-2">Nama Sub Kategori</th>
                <th class="px-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($categories as $i => $cat)
                <tr>
                  <td class="text-center px-2">{{ $i+1 }}</td>
                  <td class="px-2">{{ $cat->name }}</td>
                  <td class="px-2">
                    @if($cat->subcategories->isEmpty())
                      <span class="text-muted">-</span>
                    @else
                      <div class="d-flex flex-wrap gap-2">
                        @foreach($cat->subcategories as $sub)
                          <span class="badge bg-light text-dark d-inline-flex align-items-center p-2 rounded-2">
                            <span class="me-2">{{ $sub->name }}</span>

                            {{-- Form DELETE per chip --}}
                            <form action="{{ route('admin.subcategories.destroy', $sub->id) }}"
                                  method="POST" class="d-inline">
                              @csrf
                              @method('DELETE')

                              {{-- tombol hapus kecil: jangan type="submit" agar tidak langsung kirim --}}
                              <button type="button"
                                      class="btn btn-link badge bg-danger p-0 delete-chip-btn rounded-5 p-1"
                                      aria-label="Hapus subkategori {{ $sub->name }}"
                                      data-name="{{ $sub->name }}">
                                <i class="mdi mdi-close"></i>
                              </button>
                            </form>
                          </span>
                        @endforeach
                      </div>
                    @endif
                  </td>
                  <td class="px-2">
                    <button class="btn btn-info btn-sm text-white"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditCategory"
                            data-id="{{ $cat->id }}"
                            data-name="{{ $cat->name }}">
                      <i class="mdi mdi-pencil"></i> Edit
                    </button>

                    {{-- quick add subcategory --}}
                    <button class="btn btn-secondary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCreateSubcategory"
                            data-category="{{ $cat->id }}">
                      + Subkategori
                    </button>

                    <form id="delete-form-{{ $cat->id }}"  action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button type="button" class="btn btn-danger btn-sm text-white delete-btn" data-id="{{ $cat->id }}" data-name="Kategori {{ $cat->name }} beserta Sub Kategorinya">
                        <i class="mdi mdi-delete"></i> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Modal: Create Category --}}
      <div class="modal fade" id="modalCreateCategory" tabindex="-1">
        <div class="modal-dialog">
          <form class="modal-content" action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Tambah Kategori</h5></div>
            <div class="modal-body">
              <label class="form-label">Nama Kategori</label>
              <input type="text" name="name" class="form-control" required placeholder="mis. Peralatan Dapur">
            </div>
            <div class="modal-footer">
              <button class="btn btn-light" data-bs-dismiss="modal" type="button">Batal</button>
              <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Modal: Edit Category --}}
      <div class="modal fade" id="modalEditCategory" tabindex="-1">
        <div class="modal-dialog">
          <form class="modal-content" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
              <h5 class="modal-title">Edit Kategori</h5>
            </div>
            <div class="modal-body">
              <label class="form-label">Nama Kategori</label>
              <input type="text" name="name" id="edit-name" class="form-control" required>
            </div>
            <div class="modal-footer">
              <button class="btn btn-light" data-bs-dismiss="modal" type="button">Batal</button>
              <button class="btn btn-primary" type="submit">Update</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Modal: Create Subcategory --}}
      @php $allCats = $categories; @endphp
      <div class="modal fade" id="modalCreateSubcategory" tabindex="-1">
        <div class="modal-dialog">
          <form class="modal-content" action="{{ route('admin.subcategories.store') }}" method="POST">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Tambah Subkategori</h5></div>
            <div class="modal-body">
              <label class="form-label">Kategori</label>
              <select name="category_id" id="categorySelect" class="form-select" required>
                <option value="" disabled selected>Pilih kategori</option>
                @foreach($allCats as $c)
                  <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>

              <label class="form-label mt-3">Nama Subkategori</label>
              <input type="text" name="name" class="form-control" required placeholder="mis. Piring">
            </div>
            <div class="modal-footer">
              <button class="btn btn-light" data-bs-dismiss="modal" type="button">Batal</button>
              <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Prefill kategori saat klik "+ Subkategori" --}}
      <script>
      document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalCreateSubcategory');
        modal.addEventListener('show.bs.modal', function (event) {
          const button = event.relatedTarget;
          const catId = button?.getAttribute('data-category');
          if (catId) {
            const select = document.getElementById('categorySelect');
            select.value = catId;
          }
        });
      });
      </script>

      {{-- Script untuk isi form edit otomatis --}}
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          const editModal = document.getElementById('modalEditCategory');
          editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // tombol yang diklik
            const id   = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');

            // isi value input
            editModal.querySelector('#edit-name').value = name;

            // set action form
            editModal.querySelector('form').action = '/admin/categories/' + id;
          });
        });
      </script>

      <script>
        document.addEventListener('DOMContentLoaded', function () {
          document.querySelectorAll('.delete-chip-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
              const form = btn.closest('form');
              const name = btn.dataset.name || 'subkategori';

              Swal.fire({
                title: 'Hapus subkategori?',
                text: 'Yakin ingin menghapus "' + name + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
              }).then((result) => {
                if (result.isConfirmed) form.submit();
              });
            });
          });
        });
      </script>

    </div>
  </section>
@endsection