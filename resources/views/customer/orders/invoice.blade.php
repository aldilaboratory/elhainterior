<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Invoice {{ $order->order_code }}</title>
  <style>
    *{ font-family: DejaVu Sans, Arial, sans-serif; }
    body{ font-size:12px; color:#111; }
    .mb-2{ margin-bottom:8px; } .mb-3{ margin-bottom:12px; }
    .text-right{ text-align:right; } .text-center{ text-align:center; }
    table{ width:100%; border-collapse: collapse; }
    th,td{ padding:8px; border:1px solid #ddd; }
    th{ background:#f3f3f3; }
  </style>
</head>
<body>
    <img class="mb-2" src="data:image/png;base64, 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" />
  <h2 class="mb-2">INVOICE</h2>
  <table style="border:none;">
    <tr style="border:none;">
      <td style="border:none;">
        <strong>ELHA INTERIOR</strong><br>
        support@elhainterior.com<br>
        +62 893 664 678
      </td>
      <td class="text-right" style="border:none;">
        <strong>No. Invoice:</strong> {{ $order->order_code }}<br>
        <strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}<br>
        <strong>Status Bayar:</strong> {{ strtoupper($order->payment_status) }}
      </td>
    </tr>
  </table>

  <div class="mb-3"></div>

  <table style="border:none;">
    <tr style="border:none;">
      <td style="border:none;">
        <strong>Kepada:</strong><br>
        {{ $order->first_name }} {{ $order->last_name }}<br>
        {{ $order->address1 }}<br>
        {{ $order->postal_code }}
      </td>
      <td class="text-right" style="border:none;">
        <strong>Kontak:</strong><br>
        {{ $order->email }}<br>
        {{ $order->phone }}
      </td>
    </tr>
  </table>

  <div class="mb-3"></div>

  <table>
    <thead>
      <tr>
        <th style="width: 6%;">No</th>
        <th>Produk</th>
        <th style="width: 14%;">Harga</th>
        <th style="width: 10%;">Qty</th>
        <th style="width: 16%;">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $i => $it)
      <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ $it->name }}</td>
        <td class="text-right">Rp{{ number_format((int)$it->price,0,',','.') }}</td>
        <td class="text-center">{{ (int)$it->qty }}</td>
        <td class="text-right">Rp{{ number_format((int)$it->line_total,0,',','.') }}</td>
      </tr>
      @endforeach
      <tr>
        <td colspan="4" class="text-right"><strong>Subtotal</strong></td>
        <td class="text-right">Rp{{ number_format((int)$order->subtotal,0,',','.') }}</td>
      </tr>
      <tr>
        <td colspan="4" class="text-right"><strong>Ongkir</strong></td>
        <td class="text-right">Rp{{ number_format((int)$order->shipping,0,',','.') }}</td>
      </tr>
      <tr>
        <td colspan="4" class="text-right"><strong>Total</strong></td>
        <td class="text-right"><strong>Rp{{ number_format((int)$order->total,0,',','.') }}</strong></td>
      </tr>
    </tbody>
  </table>

  <p class="mb-2"><small>
    Metode bayar: {{ strtoupper($order->midtrans_payment_type ?? '-') }} |
    Status Midtrans: {{ strtoupper($order->midtrans_status ?? '-') }}
  </small></p>

  <p class="text-center"><small>Terima kasih telah berbelanja di ELHA INTERIOR.</small></p>
</body>
</html>
