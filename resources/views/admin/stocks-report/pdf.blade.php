<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan Stok</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h2 { margin: 0 0 8px 0; }
    .small { color: #666; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; }
    th { background: #f2f2f2; text-align: left; }
    .text-end { text-align: right; }
  </style>
</head>
<body>
  <h2>Laporan Stok</h2>
  <div class="small">
    Dicetak: {{ now()->format('d M Y H:i') }}
    @if($q) · Filter nama: “{{ $q }}” @endif
  </div>

  <div class="small" style="margin-top:6px;">
    Item: <strong>{{ number_format($summary['items']) }}</strong> ·
    Stok: <strong>{{ number_format($summary['stock_on_hand']) }}</strong> ·
    Nilai Stok: <strong>Rp{{ number_format($summary['stock_value'],0,',','.') }}</strong>
  </div>

  <table>
    <thead>
      <tr>
        <th>Produk</th>
        <th class="text-end">Harga</th>
        <th class="text-end">Stok</th>
        <th class="text-end">Nilai Stok</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $p)
        @php
          $price     = (int) ($p->price ?? 0);
          $stock     = (int) ($p->stock ?? 0);
          $stockVal  = $price * $stock;
        @endphp
        <tr>
          <td>{{ $p->name }} @if($p->category) <span class="small">({{ $p->category->name }})</span>@endif</td>
          <td class="text-end">Rp{{ number_format($price,0,',','.') }}</td>
          <td class="text-end">{{ number_format($stock) }}</td>
          <td class="text-end">Rp{{ number_format($stockVal,0,',','.') }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-end">Tidak ada data.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
