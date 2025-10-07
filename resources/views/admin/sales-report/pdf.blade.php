<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penjualan Lunas</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h2 { text-align:center; margin-bottom:10px; }
    table { width:100%; border-collapse:collapse; margin-top:10px; }
    th, td { border:1px solid #ddd; padding:8px; text-align:left; }
    th { background:#f3f3f3; }
    .text-right { text-align:right; }
  </style>
</head>
<body>
  <h2>Laporan Penjualan Lunas</h2>
  @if($from || $to)
  <p style="text-align:center;">
    Periode:
    {{ $from ? date('d/m/Y', strtotime($from)) : '-' }}
    s/d
    {{ $to ? date('d/m/Y', strtotime($to)) : '-' }}
  </p>
  @endif

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Tanggal</th>
        <th>Kode Order</th>
        <th>Customer</th>
        <th class="text-right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $order)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
        <td>{{ $order->order_code }}</td>
        <td>{{ $order->first_name }}</td>
        <td class="text-right">Rp{{ number_format($order->total,0,',','.') }}</td>
      </tr>
      @endforeach
      <tr>
        <td colspan="4" class="text-right"><strong>Total Penjualan</strong></td>
        <td class="text-right"><strong>Rp{{ number_format($totalRevenue,0,',','.') }}</strong></td>
      </tr>
    </tbody>
  </table>

  <p style="margin-top:20px; text-align:center;">
    Dicetak pada: {{ now('Asia/Makassar')->format('d M Y H:i') }} WITA
  </p>
</body>
</html>
