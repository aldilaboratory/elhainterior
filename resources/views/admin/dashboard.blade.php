@extends('layouts.admin-layout')

@section('content')
      <!-- ========== section start ========== -->
      <section class="section">
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title">
                  <h2>ELHA Interior Dashboard</h2>
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
                        ELHA Interior
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
          <div class="row">
            <div class="col-xl-4 col-lg-4 col-sm-6">
              <div class="icon-card mb-30">
                <div class="icon purple">
                  <i class="lni lni-cart-full"></i>
                </div>
                <div class="content">
                  <h6 class="mb-10">Order Baru</h6>
                  <h3 class="text-bold mb-10">{{ number_format($newOrders, 0, ',', '.') }}</h3>
                </div>
              </div>
              <!-- End Icon Cart -->
            </div>
            <!-- End Col -->
            <div class="col-xl-4 col-lg-4 col-sm-6">
              <div class="icon-card mb-30">
                <div class="icon success">
                  <i class="lni lni-dollar"></i>
                </div>
                <div class="content">
                  <h6 class="mb-10">Total Penjualan</h6>
                  <h3 class="text-bold mb-10">Rp{{ number_format($salesTotal, 0, ',', '.') }}</h3>
                </div>
              </div>
              <!-- End Icon Cart -->
            </div>
            <!-- End Col -->
            <div class="col-xl-4 col-lg-4 col-sm-6">
              <div class="icon-card mb-30">
                <div class="icon orange">
                  <i class="lni lni-user"></i>
                </div>
                <div class="content">
                  <h6 class="mb-10">Total Pengguna</h6>
                  <h3 class="text-bold mb-10">{{ number_format($userCount, 0, ',', '.') }}</h3>
                </div>
              </div>
              <!-- End Icon Cart -->
            </div>
            <!-- End Col -->
          </div>
          <!-- End Row -->
          <div class="row">
            <div class="col-12">
              <div class="card mb-30 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">Pesanan Belum Dikonfirmasi</h5>
                  <div>
                    <span class="badge bg-secondary me-2">{{ $unconfirmedCount }} pesanan</span>
                    <a class="btn btn-sm btn-outline-primary"
                      href="{{ route('admin.orders.index', ['tab' => 'unconfirmed']) }}">
                      Lihat semua
                    </a>
                  </div>
                </div>

                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                      <thead class="table-light">
                        <tr>
                          <th class="px-3">Order</th>
                          <th>Total</th>
                          <th>Customer</th>
                          <th>Status Bayar</th>
                          <th>Status Pesanan</th>
                          <th>Metode</th>
                          <th>Tanggal</th>
                          <th class="text-end pe-3">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($unconfirmed as $o)
                          <tr>
                            <td class="px-3">
                              <a href="{{ route('admin.orders.show', $o) }}" class="fw-semibold">{{ $o->order_code }}</a>
                              <div class="small text-muted">{{ $o->items_count }} item</div>
                            </td>
                            <td>Rp{{ number_format((int) $o->total, 0, ',', '.') }}</td>
                            <td>
                              <div class="fw-semibold">{{ $o->first_name }}</div>
                              <div class="small text-muted">{{ $o->email }}</div>
                            </td>
                            <td>
                              <span class="badge bg-success text-uppercase">paid</span>
                            </td>
                            <td>
                              <span class="badge bg-secondary text-uppercase">unconfirmed</span>
                            </td>
                            <td class="text-nowrap">{{ $o->midtrans_payment_type ?: '-' }}</td>
                            <td class="text-nowrap">{{ $o->created_at->format('d M Y H:i') }}</td>
                            <td class="text-end pe-3">
                              <a href="{{ route('admin.orders.show', $o) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                              <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                  Ubah Status
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                  @foreach ([
                                    'packing'   => 'Sedang Packing',
                                    'shipped'   => 'Dalam Pengiriman',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                    'rejected'  => 'Ditolak'
                                  ] as $val => $text)
                                    <li>
                                      <form method="post" action="{{ route('admin.orders.update-status', $o) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="order_status" value="{{ $val }}">
                                        <button class="dropdown-item" type="submit">{{ $text }}</button>
                                      </form>
                                    </li>
                                  @endforeach
                                </ul>
                              </div>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="8" class="text-center py-5">Belum ada pesanan yang unconfirmed.</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end container -->
      </section>
      <!-- ========== section end ========== -->
@endsection