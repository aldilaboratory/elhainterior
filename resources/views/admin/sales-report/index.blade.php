@extends('layouts.admin-layout')

@section('content')
  <div class="container-fluid py-4">
    <h4 class="fw-semibold mb-4">Laporan Penjualan</h4>

    {{-- Filter Form --}}
    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-3">
        <label class="form-label small text-muted">Dari Tanggal</label>
        <input type="date" name="from" value="{{ $from }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Sampai Tanggal</label>
        <input type="date" name="to" value="{{ $to }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Status Pembayaran</label>
        <select name="status" class="form-select">
          <option value="all" {{ $status==='all'?'selected':'' }}>Semua</option>
          <option value="paid" {{ $status==='paid'?'selected':'' }}>Lunas</option>
          <option value="pending" {{ $status==='pending'?'selected':'' }}>Pending</option>
          <option value="failed" {{ $status==='failed'?'selected':'' }}>Gagal</option>
        </select>
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-dark me-2">Tampilkan</button>
        <a href="{{ route('admin.sales-report.index') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>

    {{-- Ringkasan --}}
    <div class="card shadow-sm mb-4">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="text-muted small">Total Penjualan</div>
          <div class="fs-4 fw-semibold text-success">
            Rp{{ number_format($totalRevenue, 0, ',', '.') }}
          </div>
        </div>
        <div>
          <a href="#" class="btn btn-sm btn-outline-primary">
            <i class="ti ti-download"></i> Export PDF
          </a>
        </div>
      </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow-sm">
      <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Tanggal</th>
              <th>Kode Order</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $order)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->order_code }}</td>
                <td>{{ $order->first_name }}</td>
                <td>Rp{{ number_format($order->total,0,',','.') }}</td>
                <td>
                  @if($order->payment_status === 'paid')
                    <span class="badge bg-success">Lunas</span>
                  @elseif($order->payment_status === 'pending')
                    <span class="badge bg-warning text-dark">Pending</span>
                  @else
                    <span class="badge bg-danger">Gagal</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Tidak ada data penjualan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection