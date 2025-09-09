@extends('layouts.admin-layout')

@section('content')
  <section class="section">
    <div class="container-fluid">

      <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="title">
                <h2>Kelola Pesanan</h2>
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
      
      <!-- Tabs -->
      <ul class="nav nav-tabs mb-3 small">
        <li class="nav-item"><a class="nav-link active" href="#">Semua (20)</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Menunggu Pembayaran (6)</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Belum Dikonfirmasi (2)</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Sedang Packing (0)</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Dalam Pengiriman (8)</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Pesanan Selesai (8)</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Pengembalian Dana (2)</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Pesanan Dibatalkan (2)</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Pesanan Ditolak (2)</a></li>
      </ul>

      <!-- Search & Filters -->
      <div class="d-flex mb-3 gap-2">
        <input type="text" class="form-control" placeholder="Cari pesanan">
      </div>

      <!-- Orders Table -->
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th class="text-center">Order</th>
                <th>Total</th>
                <th>Customer</th>
                <th>Status Pembayaran</th>
                <th>Status Pesanan</th>
                <th>Metode Pembayaran</th>
                <th>Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center"><a href="#">#2453</a></td>
                <td>$87</td>
                <td>Carry Anna</td>
                <td><span class="badge bg-success">Complete</span></td>
                <td><span class="badge bg-secondary">Cancelled</span></td>
                <td>BCA</td>
                <td>Dec 12, 12:56 PM</td>
              </tr>
              <tr>
                <td class="text-center"><a href="#">#2453</a></td>
                <td>$7264</td>
                <td>Milind Mikuja</td>
                <td><span class="badge bg-danger">Cancelled</span></td>
                <td><span class="badge bg-info text-dark">Ready to Pickup</span></td>
                <td>GoPay</td>
                <td>Dec 9, 2:28 PM</td>
              </tr>
              <tr>
                <td class="text-center"><a href="#">#2453</a></td>
                <td>$375</td>
                <td>Stanly Drinkwater</td>
                <td><span class="badge bg-warning text-dark">Pending</span></td>
                <td><span class="badge bg-success">Completed</span></td>
                <td>ShopeePay</td>
                <td>Dec 4, 12:56 PM</td>
              </tr>
              <!-- Tambahkan data lainnya sesuai kebutuhan -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
@endsection