{{-- resources/views/customer/checkout/index.blade.php --}}

<x-app-layout>
  <x-slot name="header"><h2 class="fw-semibold fs-4 text-dark">Checkout</h2></x-slot>

  <section class="shop checkout section">
    <div class="container">
      {{-- SATU FORM untuk dua kolom --}}
      <form id="checkout-form" class="form" method="POST" action="{{ route('customer.checkout.store') }}">
        @csrf
        <div class="row">
          {{-- Kolom kiri: alamat --}}
          <div class="col-lg-8 col-12">
            <div class="checkout-form">
              <h2 class="mb-1">Detail Pengiriman</h2>
              <p class="text-muted mb-4">Lengkapi alamat untuk pengiriman pesanan.</p>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nama Depan <span class="text-danger">*</span></label>
                  <input type="text" name="first_name" class="form-control" required
                         value="{{ old('first_name', auth()->user()->name ?? '') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nama Belakang</label>
                  <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email <span class="text-danger">*</span></label>
                  <input type="email" name="email" class="form-control" required
                         value="{{ old('email', auth()->user()->email ?? '') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label">No. HP <span class="text-danger">*</span></label>
                  <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                </div>
                <div class="col-12">
                  <label class="form-label">Alamat 1 <span class="text-danger">*</span></label>
                  <input type="text" name="address1" class="form-control" required value="{{ old('address1') }}">
                </div>
                <div class="col-12">
                  <label for="">Kabupaten</label>
                  <select name="" id="">
                    <option value="">Denpasar</option>
                    <option value="">Badung</option>
                    <option value="">Tabanan</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Alamat 2 (opsional)</label>
                  <input type="text" name="address2" class="form-control" value="{{ old('address2') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                  <input type="text" name="postal_code" class="form-control" required value="{{ old('postal_code') }}">
                </div>
              </div>

              {{-- Hidden totals sebagai fallback (server tetap hitung ulang) --}}
              <input type="hidden" name="subtotal" value="{{ (int) $subtotal }}">
              <input type="hidden" name="shipping" value="{{ (int) $shipping }}">
              <input type="hidden" name="total"    value="{{ (int) $total }}">

              @if($errors->any())
                <div class="text-danger small mt-2">{{ $errors->first() }}</div>
              @endif
            </div>
          </div>

          {{-- Kolom kanan: ringkasan & submit --}}
          <div class="col-lg-4 col-12">
            <div class="order-details">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <h5 class="card-title mb-3">Ringkasan Pesanan</h5>

                  <h6 class="mb-2">Item</h6>
                  <div class="vstack gap-2">
                    @forelse($lines as $item)
                      <div class="d-flex align-items-center">
                        <img src="{{ $item->product->thumbnail_url ?? 'https://placehold.co/56x56' }}"
                             class="rounded me-2 mb-2" style="width:56px;height:56px;object-fit:cover;"
                             alt="{{ $item->product->name }}">
                        <div class="flex-grow-1 px-2">
                          <div class="small fw-semibold">{{ $item->product->name }}</div>
                          <div class="small text-muted">
                            {{ $item->qty }} × Rp{{ number_format($item->unit_price,0,',','.') }}
                          </div>
                        </div>
                        <div class="small fw-bold">
                          Rp{{ number_format($item->qty * $item->unit_price,0,',','.') }}
                        </div>
                      </div>
                    @empty
                      <p class="text-muted">Keranjang kosong.</p>
                    @endforelse
                  </div>

                  <ul class="list-group mt-3">
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Subtotal</span>
                      <strong>Rp{{ number_format($subtotal,0,',','.') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Ongkir</span>
                      <strong>Rp{{ number_format($shipping,0,',','.') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Total</span>
                      <strong>Rp{{ number_format($total,0,',','.') }}</strong>
                    </li>
                  </ul>

                  <div class="mt-4 d-grid gap-2">
                    <button type="submit" class="btn btn-dark text-white w-100">Bayar Sekarang</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div> {{-- /.row --}}
      </form>   {{-- SATU form ditutup di sini --}}
    </div>
  </section>
</x-app-layout>
