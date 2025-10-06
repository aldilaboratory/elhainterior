@component('mail::message')
# Terima kasih, pembayaran Anda berhasil!

Halo **{{ $order->first_name }}**,

Pembayaran Anda untuk pesanan **{{ $order->order_code }}** telah kami terima.

@component('mail::table')
| Produk | Jumlah | Harga Satuan | Subtotal |
|:--------|:--------:|:--------------:|---------:|
@foreach($order->items as $item)
| {{ $item->name }} | {{ $item->qty }} | Rp{{ number_format($item->price,0,',','.') }} | Rp{{ number_format($item->line_total,0,',','.') }} |
@endforeach
| **Ongkir** | | | Rp{{ number_format($order->shipping,0,',','.') }} |
| **Total** | | | **Rp{{ number_format($order->total,0,',','.') }}** |
@endcomponent

Anda dapat melihat detail pesanan Anda di:
@component('mail::button', ['url' => route('customer.my-orders.show', $order->order_code)])
Lihat Pesanan
@endcomponent

Terima kasih telah berbelanja di **ELHA Interior**  
Salam hangat,  
**ELHA Interior Team**
@endcomponent
