@extends('layouts.admin-layout')

@section('content')
<section class="section">
  <div class="container-fluid">

    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6"><h2>Laporan Stok</h2></div>
        <div class="col-md-6 text-md-end small text-muted">
        </div>
      </div>
    </div>

    <form method="GET" class="card shadow-sm mb-3 mt-3">
      <div class="card-body row g-2 align-items-end">
        <div class="col-sm-6">
          <label class="form-label">Cari</label>
          <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="nama produk">
        </div>
        <div class="col-sm-6 d-flex gap-2">
          <button class="btn btn-primary">Cari</button>
          {{-- <a class="btn btn-outline-secondary" href="{{ route('admin.stocks-report.index') }}">Reset</a> --}}
          <a class="btn btn-outline-success"
             href="{{ route('admin.stocks-report.pdf', request()->only('q','category_id')) }}">
            Download PDF
          </a>
        </div>
      </div>
    </form>

    <div class="card shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="text-start px-2">Produk</th>
              <th class="text-end px-2">Harga</th>
              <th class="text-end px-2">Stok</th>
              <th class="text-end px-2">Terjual Total</th>
              <th class="text-end px-2">Nilai Stok</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($products as $p)
              @php
                $price     = (int) ($p->price ?? 0);
                $stock     = (int) ($p->stock ?? 0);
                $soldTotal = (int) ($p->sold_total ?? 0);
                $stockVal  = $price * $stock;
              @endphp
              <tr>
                <td>
                  <div class="fw-semibold mx-2">{{ $p->name }}</div>
                  <div class="text-muted small mx-2">{{ optional($p->category)->name }}</div>
                </td>
                <td class="text-end px-2">Rp{{ number_format($price,0,',','.') }}</td>
                <td class="text-end px-2">{{ number_format($stock) }}</td>
                <td class="text-end px-2">{{ number_format($soldTotal) }}</td>
                <td class="text-end px-2">Rp{{ number_format($stockVal,0,',','.') }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada data.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="card-footer">
        {{ $products->links() }}
      </div>
    </div>

    <div class="col-md-12 text-md-end small text-muted mt-3">
          Item: <strong>{{ number_format($summary['items']) }}</strong> ·
          Stok: <strong>{{ number_format($summary['stock_on_hand']) }}</strong> ·
          Nilai Stok: <strong>Rp{{ number_format($summary['stock_value'],0,',','.') }}</strong>
      </div>

  </div>
</section>
@endsection
