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
              <div>
                <h2 class="mb-1">Detail Pengiriman</h2>
                <p class="text-muted mb-0">Pilih alamat tersimpan atau tambah alamat baru.</p>
              </div>
              <button type="button" class="btn btn-outline-dark btn-sm mt-3"
                      data-toggle="modal" data-target="#addAddressModal">
                + Tambah Alamat
              </button>

              {{-- Pilih alamat --}}
              <div class="mt-3">
                <label class="form-label">Alamat Tersimpan <span class="text-danger">*</span></label>
                <select class="form-select" name="shipping_address_id" id="shipping_address_id" required>
                  @forelse($addresses as $addr)
                    <option
                      value="{{ $addr->id }}"
                      {{ $addr->id == $defaultAddressId ? 'selected' : '' }}
                      data-label="{{ $addr->label }}"
                      data-is_default="{{ $addr->is_default ? 1 : 0 }}"
                      data-recipient_name="{{ $addr->recipient_name }}"
                      data-phone="{{ $addr->phone }}"
                      data-address_line="{{ $addr->address_line }}"
                      data-village="{{ $addr->village }}"
                      data-district="{{ $addr->district }}"
                      data-city="{{ $addr->city }}"
                      data-province="{{ $addr->province }}"
                      data-postal_code="{{ $addr->postal_code }}"
                    >
                      {{ $addr->label ? $addr->label.' — ' : '' }}{{ $addr->recipient_name }} | {{ $addr->phone ?? '-' }} | {{ $addr->address_line }}, {{ $addr->district ?? '' }}, {{ $addr->city ?? '' }} {{ $addr->postal_code ?? '' }} {{ $addr->is_default ? '(Default)' : '' }}
                    </option>
                  @empty
                    <option value="" disabled selected>Belum ada alamat. Tambahkan dulu.</option>
                  @endforelse
                </select>
                @error('shipping_address_id') <div class="text-danger small">{{ $message }}</div> @enderror
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
              <div class="card border-0">
                <div class="card-body">
                  <h5 class="card-title mb-3">Ringkasan Pesanan</h5>

                  <h6 class="mb-2">Item</h6>
                  <div class="vstack gap-2">
                    @forelse($lines as $item)
                      <div class="d-flex justify-content-between align-items-center">
                        <!-- Kiri: gambar + info -->
                        <div class="d-flex align-items-center">
                          <img src="{{ $item->product->thumbnail_url ?? 'https://placehold.co/56x56' }}"
                              class="rounded me-2 mb-2"
                              style="width:56px;height:56px;object-fit:cover;"
                              alt="{{ $item->product->name }}">
                          <div class="flex-grow-1 px-2">
                            <div class="small fw-semibold">{{ $item->product->name }}</div>
                            <div class="small text-muted">
                              {{ $item->qty }} × Rp{{ number_format($item->unit_price,0,',','.') }}
                            </div>
                          </div>
                        </div>

                        <!-- Kanan: total harga -->
                        <div class="small fw-bold text-end">
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

  
  
  {{-- Modal Tambah Alamat Cepat (Bootstrap 4) --}}
  <div class="modal fade" id="addAddressModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <form class="modal-content" method="POST" action="{{ route('addresses.store') }}">
        @csrf
        <div class="modal-body p-5">
          <h5 class="modal-title text-start mb-3">Tambah Alamat</h5>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Label <span class="text-danger">*</span></label>
              <input name="label"
                    class="form-control @error('label','createAddress') is-invalid @enderror"
                    value="{{ old('label') }}"
                    required
                    placeholder="Rumah / Kantor">
              @error('label','createAddress')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-md-8">
              <label>Nama Penerima <span class="text-danger">*</span></label>
              <input name="recipient_name"
                    class="form-control @error('recipient_name','createAddress') is-invalid @enderror"
                    value="{{ old('recipient_name', auth()->user()->name) }}"
                    required>
              @error('recipient_name','createAddress')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-md-12">
              <label>No. HP <span class="text-danger">*</span></label>
              <input name="phone"
                    class="form-control @error('phone','createAddress') is-invalid @enderror"
                    value="{{ old('phone', auth()->user()->phone) }}"
                    required>
              @error('phone','createAddress')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12">
              <label>Alamat <span class="text-danger">*</span></label>
              <input name="address_line"
                    class="form-control @error('address_line','createAddress') is-invalid @enderror"
                    value="{{ old('address_line') }}"
                    required
                    placeholder="Jalan, RT/RW, patokan">
              @error('address_line','createAddress')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-md-4">
              <label>Provinsi <span class="text-danger">*</span></label>
              <input name="province"
                    class="form-control @error('province','createAddress') is-invalid @enderror"
                    value="{{ old('province') }}"
                    required>
              @error('province','createAddress')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-md-4">
              <label>Kota/Kabupaten <span class="text-danger">*</span></label>
              <input name="city"
                    class="form-control @error('city','createAddress') is-invalid @enderror"
                    value="{{ old('city') }}"
                    required>
              @error('city','createAddress')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-md-4">
              <label>Kecamatan <span class="text-danger">*</span></label>
              <input name="district"
                    class="form-control @error('district','createAddress') is-invalid @enderror"
                    value="{{ old('district') }}"
                    required>
              @error('district','createAddress')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-md-6">
              <label>Kelurahan/Desa <span class="text-danger">*</span></label>
              <input name="village"
                    class="form-control @error('village','createAddress') is-invalid @enderror"
                    value="{{ old('village') }}"
                    required>
              @error('village','createAddress')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-md-6">
              <label>Kode Pos <span class="text-danger">*</span></label>
              <input name="postal_code"
                    class="form-control @error('postal_code','createAddress') is-invalid @enderror"
                    value="{{ old('postal_code') }}"
                    required>
              @error('postal_code','createAddress')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12 form-check mt-2 ms-3" style="margin-left: 20px">
              <input class="form-check-input" type="checkbox" name="is_default" id="is_default_new" value="1"
                    {{ old('is_default') ? 'checked' : '' }}>
              <label for="is_default_new" class="form-check-label">Jadikan sebagai alamat utama</label>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <button class="btn btn-dark" type="submit">Simpan Alamat</button>
        </div>
      </form>
    </div>
  </div>


  {{-- JS: sinkronkan preview saat pilihan berubah --}}
  
</x-app-layout>
