<x-app-layout>
  <x-slot name="header">
    <h2 class="fw-semibold fs-4 text-dark">Terima Kasih</h2>
  </x-slot>

  <section class="py-4">
    <div class="container">
      <div class="row g-4">

        <!-- Kartu utama -->
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                  <h3 class="mb-1">Terima kasih, pesananmu sudah tercatat!</h3>
                  <p class="text-muted mb-0">Simpan kode order berikut untuk pelacakan.</p>
                </div>

                <div class="text-end">
                  <div class="small text-muted mb-1">Kode Order</div>
                  <div class="d-flex align-items-center justify-content-end gap-2">
                    <span class="fw-bold fs-5" id="order-code">#{{ $order->order_code }}</span>
                    <button class="btn btn-sm btn-outline-secondary" id="btn-copy-code" type="button">Salin</button>
                  </div>

                  <div class="mt-2">
                    @php
                      $mapBadge = [
                        'pending'  => 'bg-warning text-dark',
                        'paid'     => 'bg-success',
                        'failed'   => 'bg-danger',
                        'refunded' => 'bg-secondary',
                      ];
                      $mapOrder = [
                        'unconfirmed' => 'bg-secondary',
                        'packing'     => 'bg-info text-dark',
                        'shipped'     => 'bg-primary',
                        'completed'   => 'bg-success',
                        'cancelled'   => 'bg-dark',
                        'rejected'    => 'bg-danger',
                      ];
                    @endphp

                    <span class="badge {{ $mapBadge[$order->payment_status] ?? 'bg-secondary' }} me-1">
                      {{ ucfirst($order->payment_status) }}
                    </span>
                    <span class="badge {{ $mapOrder[$order->order_status] ?? 'bg-secondary' }}">
                      {{ ucwords(str_replace('_',' ',$order->order_status)) }}
                    </span>
                  </div>
                </div>
              </div>

              {{-- Alert status dinamis --}}
              <div class="mt-3">
                @if($order->payment_status === 'pending')
                  <div class="alert alert-warning d-flex align-items-start" role="alert">
                    <div class="me-2">💳</div>
                    <div>
                      <strong>Menunggu pembayaran.</strong>
                      <div class="small mb-1">Segera selesaikan pembayaranmu. Setelah terbayar, status akan otomatis menjadi <em>paid</em>.</div>
                      @if($order->midtrans_order_id)
                        <div class="small text-muted">Ref: {{ $order->midtrans_order_id }}</div>
                      @endif
                    </div>
                  </div>
                @elseif($order->payment_status === 'paid')
                  <div class="alert alert-success d-flex align-items-start" role="alert">
                    <div class="me-2">✅</div>
                    <div>
                      <strong>Pembayaran diterima.</strong>
                      <div class="small mb-0">Tim kami akan memproses pesananmu (status: {{ $order->order_status }}).</div>
                    </div>
                  </div>
                @elseif($order->payment_status === 'failed')
                  <div class="alert alert-danger d-flex align-items-start" role="alert">
                    <div class="me-2">❌</div>
                    <div>
                      <strong>Pembayaran gagal.</strong>
                      <div class="small mb-0">Silakan coba metode pembayaran lain.</div>
                    </div>
                  </div>
                @elseif($order->payment_status === 'refunded')
                  <div class="alert alert-secondary d-flex align-items-start" role="alert">
                    <div class="me-2">↩️</div>
                    <div>
                      <strong>Dana dikembalikan.</strong>
                      <div class="small mb-0">Silakan cek mutasi/rekening kamu.</div>
                    </div>
                  </div>
                @endif
              </div>

              <hr class="my-4">

              {{-- Ringkasan 2 kolom: alamat & ringkasan biaya --}}
              <div class="row g-4">
                <div class="col-lg-7">
                  <h5 class="mb-3">Alamat Pengiriman</h5>
                  <div class="border rounded-3 p-3">
                    <div class="fw-semibold">{{ $order->first_name }} {{ $order->last_name }}</div>
                    <div class="small text-muted">{{ $order->email }} · {{ $order->phone }}</div>
                    <div class="mt-2">
                      {{ $order->address1 }}<br>
                      @if($order->address2) {{ $order->address2 }}<br>@endif
                      {{ $order->postal_code }}
                    </div>
                  </div>

                  <h5 class="mt-4 mb-3">Detail Item</h5>
                  <div class="vstack gap-2">
                    @foreach($order->items as $it)
                      <div class="d-flex align-items-center border rounded-3 p-2">
                        <img
                          src="{{ optional($it->product)->thumbnail_url ?? 'https://placehold.co/56x56' }}"
                          alt="{{ $it->product_name }}"
                          class="rounded me-2" style="width:56px;height:56px;object-fit:cover;"
                        >
                        <div class="flex-grow-1">
                          <div class="small fw-semibold">{{ $it->product_name }}</div>
                          <div class="small text-muted">{{ $it->qty }} × Rp{{ number_format($it->unit_price,0,',','.') }}</div>
                        </div>
                        <div class="small fw-bold">
                          Rp{{ number_format($it->line_total,0,',','.') }}
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>

                <div class="col-lg-5">
                  <h5 class="mb-3">Ringkasan Pembayaran</h5>
                  <div class="card border-0 shadow-sm">
                    <div class="card-body">
                      <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                          <span>Subtotal</span>
                          <strong>Rp{{ number_format($order->subtotal,0,',','.') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                          <span>Ongkir</span>
                          <strong>Rp{{ number_format($order->shipping,0,',','.') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                          <span>Total</span>
                          <strong>Rp{{ number_format($order->total,0,',','.') }}</strong>
                        </li>
                      </ul>

                      <div class="small text-muted">
                        Metode Pembayaran:
                        <span class="fw-semibold">{{ $order->midtrans_payment_type ? strtoupper($order->midtrans_payment_type) : '-' }}</span>
                      </div>

                      <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('customer.home') }}" class="btn btn-outline-secondary">Kembali ke Beranda</a>
                        @auth
                          <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-dark text-white">Lihat Detail Pesanan</a>
                        @endauth
                      </div>
                    </div>
                  </div>

                  @if($order->payment_status === 'pending')
                    <div class="alert alert-light border mt-3 small mb-0">
                      Ingin bayar sekarang? Integrasi Midtrans akan ditambahkan—nanti tombol bayar bisa muncul di sini.
                    </div>
                  @endif
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- Copy Kode --}}
  <script>
    (function(){
      const btn = document.getElementById('btn-copy-code');
      if (!btn) return;
      btn.addEventListener('click', async () => {
        const code = document.getElementById('order-code')?.innerText?.trim();
        if (!code) return;
        try {
          await navigator.clipboard.writeText(code.replace('#',''));
          btn.textContent = 'Tersalin!';
          setTimeout(() => btn.textContent = 'Salin', 1500);
        } catch (e) {
          alert('Gagal menyalin kode.');
        }
      });
    })();
  </script>
</x-app-layout>
