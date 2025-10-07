@extends('layouts.admin-layout')

@section('content')
<section class="section">
  <div class="container-fluid">
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6"><h2>Detail Pesanan</h2></div>
        <div class="col-md-6">
          <ol class="breadcrumb float-end mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Pesanan</a></li>
            <li class="breadcrumb-item active">{{ $order->order_code }}</li>
          </ol>
        </div>
      </div>
    </div>

    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
          <div class="card-header"><strong>Ringkasan</strong></div>
          <div class="card-body">
            <div class="d-flex gap-4 flex-wrap">
              <div>
                <div class="text-muted small">Kode Order</div>
                <div class="fw-semibold">{{ $order->order_code }}</div>
              </div>
              <div>
                <div class="text-muted small">Status Bayar</div>
                <span class="badge bg-{{ ($order->payment_status === 'paid') ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'secondary') }} text-uppercase">
                  {{ $order->payment_status }}
                </span>
              </div>
              <div>
                <div class="text-muted small">Status Pesanan</div>
                <span class="badge bg-secondary text-uppercase">{{ $order->order_status }}</span>
              </div>
              <div>
                <div class="text-muted small">Metode</div>
                <div>{{ $order->midtrans_payment_type ?: '-' }}</div>
              </div>
              <div>
                <div class="text-muted small">Tanggal</div>
                <div>{{ $order->created_at->format('d M Y H:i') }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card shadow-sm">
          <div class="card-header"><strong>Item</strong></div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table mb-0 align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Produk</th>
                    <th class="text-end">Harga</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end pe-3">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($order->items as $it)
                    <tr>
                      <td>
                        <div class="fw-semibold">{{ $it->name }}</div>
                      </td>
                      <td class="text-end">Rp{{ number_format((int)$it->price,0,',','.') }}</td>
                      <td class="text-center">{{ $it->qty }}</td>
                      <td class="text-end pe-3">Rp{{ number_format((int)$it->line_total,0,',','.') }}</td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot class="table-light">
                  <tr>
                    <th colspan="3" class="text-end">Subtotal</th>
                    <th class="text-end pe-3">Rp{{ number_format((int)$order->subtotal,0,',','.') }}</th>
                  </tr>
                  <tr>
                    <th colspan="3" class="text-end">Ongkir</th>
                    <th class="text-end pe-3">Rp{{ number_format((int)$order->shipping,0,',','.') }}</th>
                  </tr>
                  <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th class="text-end pe-3">Rp{{ number_format((int)$order->total,0,',','.') }}</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

      </div>

      <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
          <div class="card-header"><strong>Pelanggan & Alamat</strong></div>
          <div class="card-body">
            <div class="mb-2">
              <div class="fw-semibold">{{ $order->first_name }} {{ $order->last_name }}</div>
              <div class="small text-muted">{{ $order->email }} · {{ $order->phone }}</div>
            </div>
            <div class="small">
              <div>{{ $order->address1 }}</div>
              @if($order->address2)<div>{{ $order->address2 }}</div>@endif
              <div>{{ $order->postal_code }}</div>
            </div>
          </div>
        </div>

        <div class="card shadow-sm">
          <div class="card-header"><strong>Aksi</strong></div>
          <div class="card-body">
            <form method="post" action="{{ route('admin.orders.update-status', $order) }}" class="vstack gap-2">
              @csrf
              @method('PATCH')
              <label class="form-label small mb-1">Ubah Status Pesanan</label>
              <select name="order_status" class="form-select">
                @foreach (['unconfirmed'=>'Belum Dikonfirmasi','packing'=>'Sedang Packing','shipped'=>'Dalam Pengiriman','completed'=>'Selesai','cancelled'=>'Dibatalkan','rejected'=>'Ditolak'] as $val=>$text)
                  <option value="{{ $val }}" @selected($order->order_status === $val)>{{ $text }}</option>
                @endforeach
              </select>

              {{-- <label class="form-label small mb-1 mt-2">Kurir (opsional)</label>
              <input type="text" name="shipping_courier" class="form-control" value="{{ old('shipping_courier', $order->shipping_courier) }}">

              <label class="form-label small mb-1 mt-2">Resi (opsional)</label>
              <input type="text" name="tracking_code" class="form-control" value="{{ old('tracking_code', $order->tracking_code) }}"> --}}

              <button class="btn btn-primary w-100 mt-3">Simpan</button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
