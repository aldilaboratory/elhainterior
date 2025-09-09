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
                <th class="text-center">#</th>
                <th>Nama Pelanggan</th>
                <th>Email</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <!-- Row 1 -->
              <tr>
                <td class="text-center">1</td>
                <td>Ahmad Putra</td>
                <td>user@gmail.com</td>
                <td>
                  <a class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i> Hapus</a>
                </td>
              </tr>

              <!-- Row 2 -->
              <tr>
                <td class="text-center">2</td>
                <td>Budi Utomo</td>
                <td>user@gmail.com</td>
                <td>
                  <a class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i> Hapus</a>
                </td>
              </tr>

              <!-- Row 3 -->
              <tr>
                <td class="text-center">3</td>
                <td>Sefia</td>
                <td>user@gmail.com</td>
                <td>
                  <a class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i> Hapus</a>
                </td>
              </tr>

              <!-- Tambahkan baris lain sesuai kebutuhan -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
@endsection