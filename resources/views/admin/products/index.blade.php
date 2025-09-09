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
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:36px;"><input class="form-check-input" type="checkbox"></th>
                <th>Product name</th>
                <th style="width:120px;">Price</th>
                <th style="width:140px;">Category</th>
                <th>Tags</th>
                <th style="width:220px;">Vendor</th>
                <th style="width:170px;">Published on</th>
              </tr>
            </thead>
            <tbody>
              <!-- Row 1 -->
              <tr>
                <td><input class="form-check-input" type="checkbox"></td>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <img class="prod-thumb" src="https://via.placeholder.com/80x80.png?text=Watch" alt="">
                    <a href="#" class="fw-semibold text-decoration-none">Fitbit Sense Advanced Smartwatch with Tools...</a>
                  </div>
                </td>
                <td>$39</td>
                <td>Plants</td>
                <td class="text-nowrap">
                  <span class="badge rounded-pill tag-badge me-1">HEALTH</span>
                  <span class="badge rounded-pill tag-badge me-1">EXERCISE</span>
                  <span class="badge rounded-pill tag-badge me-1">FITNESS</span>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-link text-warning p-0"><i class="bi bi-star"></i></button>
                    <a href="#" class="link-primary">Blue Olive Plant sellers. Inc</a>
                  </div>
                </td>
                <td>Nov 12, 10:45 PM</td>
              </tr>

              <!-- Row 2 -->
              <tr>
                <td><input class="form-check-input" type="checkbox"></td>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <img class="prod-thumb" src="https://via.placeholder.com/80x80.png?text=Phone" alt="">
                    <a href="#" class="fw-semibold text-decoration-none">iPhone 13 pro max-Pacific Blue-128GB storage</a>
                  </div>
                </td>
                <td>$87</td>
                <td>Furniture</td>
                <td class="text-nowrap">
                  <span class="badge rounded-pill tag-badge me-1">PRO</span>
                  <span class="badge rounded-pill tag-badge me-1">CAMERA</span>
                  <span class="badge rounded-pill tag-badge me-1">SWAG</span>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-link text-warning p-0"><i class="bi bi-star-fill"></i></button>
                    <a href="#" class="link-primary">Beatrice Furnitures</a>
                  </div>
                </td>
                <td>Nov 11, 7:36 PM</td>
              </tr>

              <!-- Row 3 -->
              <tr>
                <td><input class="form-check-input" type="checkbox"></td>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <img class="prod-thumb" src="https://via.placeholder.com/80x80.png?text=MBP" alt="">
                    <a href="#" class="fw-semibold text-decoration-none">Apple MacBook Pro 13 inch-M1-8/256GB-space</a>
                  </div>
                </td>
                <td>$9</td>
                <td>Plants</td>
                <td class="text-nowrap">
                  <span class="badge rounded-pill tag-badge me-1">EFFICIENCY</span>
                  <span class="badge rounded-pill tag-badge me-1">APPLE</span>
                  <span class="badge rounded-pill tag-badge me-1">HANDY</span>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-link text-warning p-0"><i class="bi bi-star"></i></button>
                    <a href="#" class="link-primary">PlantPlanet</a>
                  </div>
                </td>
                <td>Nov 11, 8:16 AM</td>
              </tr>

              <!-- Tambahkan baris lain sesuai kebutuhan -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
@endsection