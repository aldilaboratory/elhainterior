<x-app-layout>
    <x-slot name="header"><h2 class="fw-semibold fs-4 text-dark">Cart</h2></x-slot>

    <div class="shopping-cart section">
      <div class="container">
        <div class="row"><div class="col-12">

          <table class="table shopping-summery">
            <thead>
              <tr class="main-hading">
                <th class="text-center text-white">FOTO</th>
                <th class="text-center text-white">PRODUK</th>
                <th class="text-center text-white">HARGA</th>
                <th class="text-center text-white">KUANTITAS</th>
                <th class="text-center text-white">TOTAL</th>
                <th class="text-center text-white">HAPUS</th>
              </tr>
            </thead>
            <tbody>
            @foreach($lines as $item)
              @php
                $stock = (int) $item->product->stock;
                $max   = max(1, $stock);
              @endphp
              <tr data-item-row="{{ $item->id }}">
                <td class="image">
                  <img src="{{ $item->product->thumbnail_url ?? 'https://placehold.co/100x100' }}"
                       alt="{{ $item->product->name }}"
                       style="width:100px;height:100px;object-fit:cover;border-radius:6px" />
                </td>

                <td class="product-des">
                  <p class="product-name">
                    <a href="{{ route('customer.product-details', $item->product->slug) }}">{{ $item->product->name }}</a>
                  </p>
                  {{-- <small class="text-muted">Stok tersedia: <span class="item-stock">{{ $stock }}</span></small> --}}
                </td>

                <td class="price">
                  <span>Rp{{ number_format($item->product->price,0,',','.') }}</span>
                </td>

                <td class="qty align-middle">
					<div class="quantity">
						<div class="input-group">
						<div class="button minus">
							<button type="button"
									class="btn btn-primary btn-number"
									data-type="minus"
									data-item="{{ $item->id }}"
									{{ $item->qty <= 1 ? 'disabled' : '' }}>
							<i class="ti-minus"></i>
							</button>
						</div>

						@php
							$minQty  = $item->product->stock > 0 ? 1 : 0;
							$maxQty  = $item->product->stock > 0 ? $item->product->stock : 0;
						@endphp

						<input type="text"
								class="input-number"
								value="{{ $item->qty }}"
								data-item="{{ $item->id }}"
								data-min="{{ $minQty }}"
								data-max="{{ $maxQty }}"
								> {{-- biar perubahan hanya dari tombol, menghindari trigger ganda --}}

						<div class="button plus">
							<button type="button"
									class="btn btn-primary btn-number"
									data-type="plus"
									data-item="{{ $item->id }}"
									{{ $item->qty >= $maxQty ? 'disabled' : '' }}>
							<i class="ti-plus"></i>
							</button>
						</div>
						</div>

						<p class="availability mt-2">
						Ketersediaan :
						@if($item->product->stock > 0)
							<span class="text-success item-stock">{{ $item->product->stock }} stok</span>
						@else
							<span class="text-danger">Habis</span>
						@endif
						</p>
					</div>
				</td>

                <td class="total-amount">
                  <span class="item-total" data-item="{{ $item->id }}">
                    Rp{{ number_format($item->qty * $item->unit_price,0,',','.') }}
                  </span>
                </td>

                <td class="action">
                  <form action="{{ route('cart.item.remove', $item) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="text-danger btn-lg" aria-label="Hapus item"><i class="ti-trash"></i></button>
                  </form>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>

        </div></div>

        <div class="row">
          <div class="col-12">
            <div class="total-amount">
              <div class="row">
                <div class="col-lg-8 col-md-5 col-12"></div>
                <div class="col-lg-4 col-md-7 col-12">
                  <div class="right">
                    <ul>
                      <li>Cart Subtotal
                        <span id="cart-subtotal"
                              data-subtotal="{{ (int) $subtotal }}">
                          Rp{{ number_format($subtotal,0,',','.') }}
                        </span>
                      </li>
                    </ul>
                    <div class="button5">
                      <a href="{{ route('customer.checkout') }}" class="btn btn-dark text-white w-100">Checkout</a>
                      <a href="{{ route('customer.home') }}" class="btn bg-white text-dark w-100" style="border:1px solid #333">Lanjut Belanja</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>    
          </div>
        </div>

      </div>
    </div>

    {{-- JS: minus/plus + AJAX update --}}
<script>
(() => {
  const csrf = '{{ csrf_token() }}';

  const rupiah = n => (new Intl.NumberFormat('id-ID').format(n||0));

  const clamp = (val, min, max) => {
    val = parseInt(val, 10);
    if (isNaN(val)) val = min;
    return Math.max(min, Math.min(max, val));
  };

  async function sendUpdate(itemId, qty){
    const url = "{{ url('/cart/item') }}/" + itemId + "/update";
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ qty })
    });
    if (!res.ok) throw new Error('Gagal update');
    return res.json();
  }

  function syncButtons(wrapper){
    const input = wrapper.querySelector('.input-number');
    const minus = wrapper.querySelector('.btn-number[data-type="minus"]');
    const plus  = wrapper.querySelector('.btn-number[data-type="plus"]');
    const val   = parseInt(input.value, 10);
    const min   = parseInt(input.dataset.min, 10);
    const max   = parseInt(input.dataset.max, 10);

    minus.disabled = val <= min;
    plus.disabled  = val >= max && max > 0;
  }

  async function handleStep(btn, step){
    // wrapper = .input-group yang berisi tombol dan input
    const wrapper  = btn.closest('.input-group');
    const input    = wrapper.querySelector('.input-number');
    const itemId   = input.dataset.item;
    const min      = parseInt(input.dataset.min, 10);
    const max      = parseInt(input.dataset.max, 10);
    const next     = clamp(parseInt(input.value, 10) + step, min, max);

    // jika tidak berubah, cukup sinkronkan tombol
    if (next === parseInt(input.value,10)) { syncButtons(wrapper); return; }

    input.value = next;        // update cepat di UI
    syncButtons(wrapper);      // sinkron tombol

    try{
      const data = await sendUpdate(itemId, next);

      // pakai qty & stok dari server (lebih aman)
      input.value        = data.qty;
      input.dataset.max  = data.stock > 0 ? data.stock : 0;
      syncButtons(wrapper);

      // update line total
      const row = btn.closest('tr[data-item-row]');
      row.querySelector('.item-total').textContent = 'Rp' + rupiah(data.line_total);

      // update subtotal
      const subEl = document.getElementById('cart-subtotal');
      subEl.dataset.subtotal = data.subtotal;
      subEl.textContent = 'Rp' + rupiah(data.subtotal);
      document.getElementById('cart-total').textContent = 'Rp' + rupiah(data.subtotal);

      // stok teks (opsional)
      row.querySelector('.item-stock')?.replaceChildren(document.createTextNode(data.stock + ' stok'));
    }catch(e){
      // balikan UI jika gagal
      input.value = clamp(parseInt(input.value,10) - step, min, max);
      syncButtons(wrapper);
      alert('Gagal memperbarui keranjang. Coba lagi.');
    }
  }

  // SATU handler untuk +/–
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.quantity .btn-number');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation(); // cegah handler bawaan theme ikut jalan

    const type = btn.dataset.type;
    handleStep(btn, type === 'plus' ? +1 : -1);
  });

  // inisialisasi state tombol awal
  document.querySelectorAll('.quantity .input-group').forEach(syncButtons);
})();
</script>

</x-app-layout>
