@extends('layouts.admin-layout')

@section('content')
<section class="section">
  <div class="container-fluid">

    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6"><h2>Kelola Pesanan</h2></div>
        <div class="col-md-6">
          <ol class="breadcrumb float-end mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pesanan</li>
          </ol>
        </div>
      </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3 small">
      @foreach ($tabs as $key => $label)
        <li class="nav-item">
          <a class="nav-link {{ $tab === $key ? 'active' : '' }}"
             href="{{ route('admin.orders.index', ['tab'=>$key,'search'=>$search]) }}">
            {{ $label }}
            <span class="badge bg-secondary ms-1">{{ $stats[$key] ?? 0 }}</span>
          </a>
        </li>
      @endforeach
    </ul>

    {{-- Search --}}
    <form class="d-flex mb-3 gap-2" method="get" action="{{ route('admin.orders.index') }}">
      <input type="hidden" name="tab" value="{{ $tab }}">
      <input type="text" class="form-control" name="search" placeholder="Cari kode / email / nama / telp"
             value="{{ $search }}">
      <button class="btn btn-outline-secondary">Cari</button>
      @if($search)
        <a class="btn btn-link" href="{{ route('admin.orders.index', ['tab'=>$tab]) }}">Reset</a>
      @endif
    </form>

    {{-- Flash --}}
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="card shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="px-3">Order</th>
                <th>Total</th>
                <th>Customer</th>
                <th>Status Pembayaran</th>
                <th>Status Pesanan</th>
                <th>Metode</th>
                <th>Tanggal</th>
                <th class="text-end pe-3">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($orders as $o)
                <tr>
                  <td class="px-3">
                    <a href="{{ route('admin.orders.show', $o) }}" class="fw-semibold">{{ $o->order_code }}</a>
                    <div class="small text-muted">{{ $o->items_count }} item</div>
                  </td>
                  <td>Rp{{ number_format((int)$o->total, 0, ',', '.') }}</td>
                  <td>
                    <div class="fw-semibold">{{ $o->first_name }}</div>
                    <div class="small text-muted">{{ $o->email }}</div>
                  </td>
                  <td>
                    @php $pb = $payBadge[$o->payment_status] ?? 'secondary'; @endphp
                    <span class="badge bg-{{ $pb }} text-uppercase">{{ $o->payment_status }}</span>
                  </td>
                  <td>
                    @php $ob = $orderBadge[$o->order_status] ?? 'secondary'; @endphp
                    <span class="badge bg-{{ $ob }} text-uppercase">{{ $o->order_status }}</span>
                  </td>
                  <td class="text-nowrap">{{ $o->midtrans_payment_type ?: '-' }}</td>
                  <td class="text-nowrap">{{ $o->created_at->format('d M Y H:i') }}</td>
                  <td class="text-end pe-3">

                    <a href="{{ route('admin.orders.show', $o) }}" class="btn btn-sm btn-outline-secondary">
                      Detail
                    </a>

                    {{-- Dropdown Ubah Status --}}
                    <div class="btn-group">
                      <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        Ubah Status
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        @foreach (['unconfirmed'=>'Belum Dikonfirmasi','packing'=>'Sedang Packing','shipped'=>'Dalam Pengiriman','completed'=>'Selesai','cancelled'=>'Dibatalkan','rejected'=>'Ditolak'] as $val=>$text)
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
                <tr><td class="text-center py-5" colspan="8">Belum ada pesanan.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer">
        {{ $orders->links() }}
      </div>
    </div>

  </div>
</section>
@endsection
