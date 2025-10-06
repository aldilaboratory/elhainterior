<x-app-layout>
  <x-slot name="header">
    <div class="d-flex align-items-center justify-content-between">
      <h2 class="fw-semibold fs-4 text-dark mb-0">Detail Pesanan {{ $order->order_code }}</h2>
      <a href="{{ route('customer.my-orders') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
    </div>
  </x-slot>

  <section class="section">
    <div class="container">
      <div class="row g-3">
        <div class="col-lg-8">
          <div class="card border-0 h-100">
            <div class="card-body">
              <h5 class="mb-3">Item</h5>
              <div class="vstack gap-3">
                @foreach($order->items as $it)
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <img src="{{ $it->product->thumbnail_url ?? 'https://placehold.co/56x56' }}"
                           class="rounded me-2" style="width:56px;height:56px;object-fit:cover;">
                      <div>
                        <div class="fw-semibold">{{ $it->name }}</div>
                        <div class="small text-muted">
                          {{ $it->qty }} × Rp{{ number_format($it->price,0,',','.') }}
                        </div>
                      </div>
                    </div>
                    <div class="fw-semibold">
                      Rp{{ number_format($it->line_total,0,',','.') }}
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card border-0">
            <div class="card-body">
              <h5 class="mb-3">Ringkasan</h5>
              <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between">
                  <span>Subtotal</span>
                  <strong>Rp{{ number_format($order->subtotal,0,',','.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                  <span>Ongkir ({{ strtoupper($order->courier_code) }} {{ $order->courier_service }})</span>
                  <strong>Rp{{ number_format($order->shipping,0,',','.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                  <span>Total</span>
                  <strong>Rp{{ number_format($order->total,0,',','.') }}</strong>
                </li>
              </ul>

              <div class="mb-3">
                <div class="small text-muted">Status Pembayaran</div>
                <div class="fw-semibold text-uppercase">{{ $order->payment_status }}</div>
              </div>
              <div class="mb-3">
                <div class="small text-muted">Status Pesanan</div>
                <div class="fw-semibold text-uppercase">{{ $order->order_status }}</div>
              </div>

              <h6 class="mt-4">Alamat Pengiriman</h6>
              <div class="small">
                {{ $order->first_name }}<br>
                {{ $order->phone }}<br>
                {{ $order->address1 }}<br>
                {{ $order->ship_to_region_label }}
                @if($order->postal_code) {{ ', '.$order->postal_code }} @endif
              </div>

              @if ($order->payment_status === 'pending')
                <a href="{{ route('customer.pay.snap', $order) }}" class="btn btn-dark w-100 mt-3">Bayar Sekarang</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</x-app-layout>
