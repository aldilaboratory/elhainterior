@extends('layouts.admin-layout')

@section('content')
  <section class="section">
    <div class="container-fluid">

      <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="title">
                <h2>Kelola Admin</h2>
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
                      Admin
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
          <a href="{{ route('admin.data-admin.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>+ Tambah admin baru</a>
        </div>
      </div>

      <!-- Filters -->
      <div class="row g-2 mb-3">
        <div class="col-12 col-lg-12">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Cari admin...">
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
                <th class="px-2">Nama Admin</th>
                <th class="px-2">Email</th>
                <th class="px-2">Role</th>
                <th class="px-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($admins as $admin)
                <tr>
                  <td class="text-center px-2">{{ $loop->iteration }}</td>
                  <td class="px-2">{{ $admin->name }}</td>
                  <td class="px-2">{{ $admin->email }}</td>
                  <td class="px-2">{{ $admin->role }}</td>
                  <td class="px-2">
                    <a href="{{ route('admin.data-admin.edit', $admin->id) }}" class="btn btn-info btn-sm text-white"><i class="mdi mdi-pencil"></i> Edit</a>
                    <form id="delete-form-{{ $admin->id }}" action="{{ route('admin.data-admin.destroy', $admin->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="button" data-id="{{ $admin->id }}" data-name="{{ $admin->name }}" class="btn btn-danger btn-sm text-white delete-btn"><i class="mdi mdi-delete"></i> Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center">Tidak ada data</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
@endsection