@extends('layouts.admin-layout')

@section('content')
  <section class="section">
    <div class="container-fluid">

      <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="title">
                <h2>Kelola Pelanggan</h2>
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
                      Pelanggan
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

      <!-- Filters -->
      <div class="row g-2 mb-3">
        <div class="col-12 col-lg-12">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Cari pelanggan...">
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
                <th class="px-2">Nama Pelanggan</th>
                <th class="px-2">Email</th>
                <th class="px-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($customers as $customer)
                <tr>
                  <td class="text-center px-2">{{ $loop->iteration }}</td>
                  <td class="px-2">{{ $customer->name }}</td>
                  <td class="px-2">{{ $customer->email }}</td>
                  <td class="px-2">
                    <a class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i> Hapus</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center">Tidak ada data</td>
                </tr>
              @endforelse
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
@endsection