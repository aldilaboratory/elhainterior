@extends('layouts.admin-layout')

@section('content')
<section class="section">
  <div class="container-fluid">

    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6"><h2>Laporan Stok</h2></div>
        <div class="col-md-6 text-md-end small text-muted">
          Item: <strong>{{ number_format($summary['items']) }}</strong> ·
          Stok: <strong>{{ number_format($summary['stock_on_hand']) }}</strong> ·
          Nilai Stok: <strong>Rp{{ number_format($summary['stock_value'],0,',','.') }}</strong>
        </div>
      </div>
    </div>

    <form method="GET" class="card shadow-sm mb-3">
      <div class="card-body row g-2 align-items-end">
        <div class="col-sm-4">
          <label class="form-label">Cari</label>
          <input type="text" name="q" value="{{ $q }}" class="form-control"
                 placeholder="nama / SKU">
        </div>
        <div class="col-sm-2">
          <label class="form-label">Periode (hari)</label>
          <input type="number" min="1" name="days" value="{{ $days }}" class="form-control">
        </div>
        <div class="col-sm-2">
          <label class="form-label">Low stock &le;</label>
          <input type="number" min="0" name="low_min" value="{{ $lowStockMin }}" class="form-control">
        </div>
        {{-- contoh: kalau punya category list, tampilkan --}}
        {{-- <div class="col-sm-3">
          <label class="form-label">Kategori</label>
          <select name="category_id" class="form-select">
            <option value="">Semua</option>
            @foreach(\App\Models\Category::orderBy('name')->get() as $c)
              <option value="{{ $c->id }}" {{ $categoryId==$c->id?'selected':'' }}>
                {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div> --}}
        <div class="col-sm-4 d-flex gap-2">
          <button class="btn btn-primary">Terapkan</button>
          <a class="btn btn-outline-secondary" href="{{ route('admin.stocks-report.index') }}">Reset</a>
          <a class="btn btn-outline-success"
             href="{{ request()->fullUrlWithQuery(['export'=>'csv']) }}">
            Export CSV
          </a>
        </div>
      </div>
    </form>

    <div class="card shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Produk</th>
              <th>SKU</th>
              <th class="text-end">Harga</th>
              <th class="text-end">Stok</th>
              <th class="text-end">Terjual {{ $days }}h</th>
              <th class="text-end">Terjual Total</th>
              <th class="text-end">Nilai Stok</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($products as $p)
              @php
                $price     = (int) ($p->price ?? 0);
                $stock     = (int) ($p->stock ?? 0);
                $soldN     = (int) ($p->sold_ndays ?? 0);
                $soldTotal = (int) ($p->sold_total ?? 0);
                $stockVal  = $price * $stock;
                $low       = $stock <= $lowStockMin;
              @endphp
              <tr>
                <td>
                  <div class="fw-semibold">{{ $p->name }}</div>
                  <div class="text-muted small">{{ optional($p->category)->name }}</div>
                </td>
                <td class="text-muted">{{ $p->sku }}</td>
                <td class="text-end">Rp{{ number_format($price,0,',','.') }}</td>
                <td class="text-end {{ $low ? 'text-danger fw-semibold' : '' }}">
                  {{ number_format($stock) }}
                </td>
                <td class="text-end">{{ number_format($soldN) }}</td>
                <td class="text-end">{{ number_format($soldTotal) }}</td>
                <td class="text-end">Rp{{ number_format($stockVal,0,',','.') }}</td>
                <td>
                  @if($low)
                    <span class="badge bg-danger">Low</span>
                  @else
                    <span class="badge bg-success">OK</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="card-footer">
        {{ $products->links() }}
      </div>
    </div>

  </div>
</section>
@endsection
