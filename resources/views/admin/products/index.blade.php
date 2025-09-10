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
                      eCommerce
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
          <button class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>+ Tambah produk baru</button>
        </div>
      </div>

      <!-- Filters -->
      <div class="row g-2 mb-3">
        <div class="col-12 col-lg-6">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Cari produk">
          </div>
        </div>
        <div class="col-6 col-lg-6">
          <button class="btn w-100 btn-outline-secondary d-flex justify-content-between align-items-center" data-bs-toggle="dropdown">
            <span>Category</span><i class="bi bi-chevron-down small"></i>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">All</a></li>
            <li><a class="dropdown-item" href="#">Plants</a></li>
            <li><a class="dropdown-item" href="#">Fashion</a></li>
            <li><a class="dropdown-item" href="#">Gadgets</a></li>
          </ul>
        </div>
      </div>

      <!-- Table -->
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th style="width:36px;" class="text-center px-2">#</th>
                <th class="px-2">Nama Produk</th>
                <th class="px-2">Harga</th>
                <th class="px-2">Kategori</th>
                <th class="px-2">Sub Kategori</th>
                <th class="px-2">Vendor</th>
                <th class="px-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <!-- Row 1 -->
              <tr>
                <td class="text-center px-2">1</td>
                <td class="px-2">
                  <div class="d-flex align-items-center gap-3">
                    <img class="prod-thumb" src="https://placehold.co/80x80" alt="">
                    <a href="#" class="fw-semibold text-decoration-none">Fitbit Sense Advanced Smartwatch with Tools...</a>
                  </div>
                </td>
                <td class="px-2">$39</td>
                <td class="px-2">Plants</td>
                <td class="px-2">Rose</td>
                <td class="px-2">
                  <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-link text-warning p-0"><i class="bi bi-star"></i></button>
                    <a href="#" class="link-primary">Blue Olive Plant sellers. Inc</a>
                  </div>
                </td>
                <td class="px-2">
                  <a class="btn btn-info btn-sm text-white"><i class="mdi mdi-pencil"></i> Edit</a>
                  <a class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i> Hapus</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
@endsection