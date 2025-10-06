{{-- resources/views/customer/checkout/index.blade.php --}}
<x-app-layout>
  <x-slot name="header"><h2 class="fw-semibold fs-4 text-dark">Checkout</h2></x-slot>

  <section class="shop checkout section">
    <div class="container">
      <form id="checkout-form" class="form" method="POST" action="{{ route('customer.checkout.store') }}">
        @csrf
        <input type="hidden" name="addr_mode" id="addr_mode_field" value="saved">

        <div class="row">
          {{-- =================== KIRI: ALAMAT & KURIR =================== --}}
          <div class="col-lg-8 col-12">
            <div class="checkout-form">
              <div>
                <h2 class="mb-1">Detail Pengiriman</h2>
                <p class="text-muted mb-0">Pilih alamat tersimpan atau buat alamat baru.</p>
              </div>

              {{-- ===== PILIH MODE ALAMAT ===== --}}
              <div class="mt-3">
                <label class="form-label d-block mb-2">Alamat Pengiriman <span class="text-danger">*</span></label>

                <div class="d-flex align-items-center mb-2 gap-3">
                  <div class="form-check">
                    <input class="form-check-input me-0" type="radio" name="addr_mode_radio" id="addr_mode_saved" value="saved" checked>
                    <label class="form-check-label p-0" for="addr_mode_saved">Gunakan alamat tersimpan</label>
                  </div>
                  <div class="form-check ms-3">
                    <input class="form-check-input me-0" type="radio" name="addr_mode_radio" id="addr_mode_new" value="new">
                    <label class="form-check-label p-0" for="addr_mode_new">Tambah alamat baru</label>
                  </div>
                </div>

                {{-- ===== ALAMAT TERSIMPAN ===== --}}
                <div id="savedBox">
                  <select class="form-select" name="shipping_address_id" id="shipping_address_id" required>
                    @forelse($addresses as $addr)
                      <option
                        value="{{ $addr->id }}"
                        {{ $addr->id == $defaultAddressId ? 'selected' : '' }}
                        data-has-dest="{{ $addr->destination_id ? 1 : 0 }}"
                      >
                        {{ $addr->label ? $addr->label.' — ' : '' }}
                        {{ $addr->recipient_name }} | {{ $addr->phone ?? '-' }} | {{ $addr->address_line }}
                        @if(!$addr->destination_id) — (Belum siap ongkir) @endif
                      </option>
                    @empty
                      <option value="" disabled selected>Belum ada alamat.</option>
                    @endforelse
                  </select>
                </div>

                {{-- ===== TAMBAH ALAMAT BARU ===== --}}
                <div id="newAddressBox" class="border rounded p-3 mt-3" style="display:none;">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">Label <span class="text-danger">*</span></label>
                      <input class="form-control na-field" id="na_label" name="na_label" placeholder="Rumah / Kantor" disabled>
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                      <input class="form-control na-field" id="na_recipient" name="na_recipient" value="{{ auth()->user()->name }}" disabled>
                    </div>
                    <div class="col-md-12">
                      <label class="form-label">No. HP <span class="text-danger">*</span></label>
                      <input class="form-control na-field" id="na_phone" name="na_phone" value="{{ auth()->user()->phone }}" disabled>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Alamat (jalan/rt/rw/patokan) <span class="text-danger">*</span></label>
                      <input class="form-control na-field" id="na_address" name="na_address" placeholder="Jl. Contoh No. 1 RT 01/02" disabled>
                    </div>

                    {{-- AUTOCOMPLETE TUJUAN --}}
                    <div class="col-12">
                      <label class="form-label">Tujuan (Komerce/RajaOngkir V2) <span class="text-danger">*</span></label>
                      <input class="form-control na-field" id="na_dest_search" placeholder="kuta / denpasar / tebet..." autocomplete="off" disabled>
                      <small class="text-muted">Pilih salah satu hasil.</small>
                      <div id="na_dest_results" class="list-group mt-1" style="max-height:220px; overflow:auto; display:none;"></div>
                      <input type="hidden" id="na_destination_id" name="na_destination_id">
                      <input type="hidden" id="na_destination_label" name="na_destination_label">
                    </div>

                    <div class="col-12 form-check mt-2">
                      <input class="form-check-input na-field" type="checkbox" id="na_is_default" name="na_is_default" value="1" disabled>
                      <label for="na_is_default" class="form-check-label">Jadikan sebagai alamat utama</label>
                    </div>
                  </div>
                </div>
              </div>

              {{-- ===== ERROR LIST (server) ===== --}}
              @if ($errors->any())
                <div class="alert alert-danger mt-3">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                      <li>{{ $err }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              {{-- ===== KURIR + LAYANAN ===== --}}
              <div class="mt-4">
                <label class="form-label">Kurir</label>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <select id="courier" class="form-select" style="max-width:220px">
                    <option value="" selected disabled>Pilih Jasa Ongkir</option>
                    <option value="jne">JNE</option>
                    <option value="tiki">TIKI</option>
                    <option value="pos">POS</option>
                    <option value="sicepat">SiCepat</option>
                    <option value="anteraja">AnterAja</option>
                  </select>
                  <button type="button" id="btn_quote" class="btn btn-outline-primary">Hitung Ongkir</button>
                </div>
                <div id="services" class="mt-4 small text-muted">—</div>
              </div>

              {{-- ===== HIDDEN FIELDS UNTUK SUBMIT ===== --}}
              <input type="hidden" name="courier_code"    id="f_courier_code">
              <input type="hidden" name="courier_service" id="f_courier_service">
              <input type="hidden" name="shipping"        id="f_shipping">
              <input type="hidden" name="shipping_etd"    id="f_shipping_etd">
              <input type="hidden" name="subtotal" value="{{ (int) $subtotal }}">
              <input type="hidden" name="total"    value="{{ (int) $total }}">
            </div>
          </div>

          {{-- =================== KANAN: RINGKASAN =================== --}}
          <div class="col-lg-4 col-12">
            <div class="order-details">
              <div class="card border-0">
                <div class="card-body">
                  <h5 class="card-title mb-3">Ringkasan Pesanan</h5>

                  <h6 class="mb-2">Item</h6>
                  <div class="vstack gap-2">
                    @foreach($lines as $item)
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                          <img src="{{ $item->product->thumbnail_url ?? 'https://placehold.co/56x56' }}"
                               class="rounded me-2 mb-2"
                               style="width:56px;height:56px;object-fit:cover;"
                               alt="{{ $item->product->name }}">
                          <div class="flex-grow-1 px-2">
                            <div class="small fw-semibold">{{ $item->product->name }}</div>
                            <div class="small text-muted">
                              {{ $item->qty }} × Rp{{ number_format($item->unit_price,0,',','.') }}
                              @if($item->product->weight)
                                <span class="text-muted ms-1">({{ number_format($item->product->weight * $item->qty, 0, ',', '.') }}g)</span>
                              @endif
                            </div>
                          </div>
                        </div>
                        <div class="small fw-bold text-end">
                          Rp{{ number_format($item->qty * $item->unit_price,0,',','.') }}
                        </div>
                      </div>
                    @endforeach
                  </div>

                  @if($totalWeightGram > 0)
                    <div class="mt-2 mb-1 small text-muted">
                      <strong>Total Berat:</strong>
                      @if($totalWeightGram >= 1000)
                        {{ number_format($totalWeightGram / 1000, 2, ',', '.') }} kg
                      @else
                        {{ number_format($totalWeightGram, 0, ',', '.') }} gram
                      @endif
                    </div>
                  @endif

                  <ul class="list-group mt-3">
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Subtotal</span>
                      <strong>Rp{{ number_format($subtotal,0,',','.') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Ongkir</span>
                      <strong id="ui_shipping">Rp0</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Total</span>
                      <strong id="ui_total">Rp{{ number_format($subtotal,0,',','.') }}</strong>
                    </li>
                  </ul>

                  <div class="mt-4 d-grid">
                    <button type="submit" class="btn btn-dark w-100" id="btn_pay" disabled>Bayar Sekarang</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> {{-- /.row --}}
      </form>
    </div>
  </section>

  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', function(){
    // ===== Refs
    const servicesWrap = document.getElementById('services');
    const uiShip = document.getElementById('ui_shipping');
    const uiTotal= document.getElementById('ui_total');
    const fCourier = document.getElementById('f_courier_code');
    const fService = document.getElementById('f_courier_service');
    const fShip    = document.getElementById('f_shipping');
    const fEtd     = document.getElementById('f_shipping_etd');
    const SUBTOTAL = {{ (int) $subtotal }};
    const modeSaved   = document.getElementById('addr_mode_saved');
    const modeNew     = document.getElementById('addr_mode_new');
    const savedBox    = document.getElementById('savedBox');
    const newBox      = document.getElementById('newAddressBox');
    const selectSaved = document.getElementById('shipping_address_id');
    const addrModeFld = document.getElementById('addr_mode_field');
    const courierSel  = document.getElementById('courier');
    const qInput   = document.getElementById('na_dest_search');
    const listWrap = document.getElementById('na_dest_results');
    const hidId    = document.getElementById('na_destination_id');
    const hidLbl   = document.getElementById('na_destination_label');
    const btnQuote = document.getElementById('btn_quote');
    const btnPay   = document.getElementById('btn_pay');

    // field alamat-baru (supaya bisa enable/disable massal)
    const naFields = document.querySelectorAll('.na-field');

    function setNAEnabled(enabled){
      naFields.forEach(el => { el.disabled = !enabled; el.required = enabled; });
    }

    function updatePayButtonState(){
      const ready = fCourier.value && fService.value && fShip.value;
      btnPay.disabled = !ready;
    }

    // ===== Utils
    function rupiah(n){ return 'Rp' + Number(n||0).toLocaleString('id-ID'); }
    function resetShippingUI(){
      servicesWrap.textContent = '—';
      uiShip.textContent  = rupiah(0);
      uiTotal.textContent = rupiah(SUBTOTAL);
      fCourier.value = fService.value = fShip.value = fEtd.value = '';
      updatePayButtonState();
    }
    updatePayButtonState();

    // ===== Mode switch
    function setMode(mode){
      const isNew = (mode === 'new');
      addrModeFld.value = isNew ? 'new' : 'saved';
      newBox.style.display   = isNew ? 'block' : 'none';
      savedBox.style.display = isNew ? 'none'  : 'block';
      setNAEnabled(isNew);          // penting: supaya na_* tidak ikut submit saat saved
      resetShippingUI();
    }
    modeSaved.addEventListener('change', ()=> setMode('saved'));
    modeNew  .addEventListener('change', ()=> setMode('new'));

    // ===== Autocomplete tujuan (NEW)
    let debTimer = null, lastQ = '';
    function renderResults(items) {
      listWrap.innerHTML = '';
      if (!items?.length) { listWrap.style.display = 'none'; return; }
      items.forEach(it => {
        const a = document.createElement('a');
        a.href = 'javascript:void(0)';
        a.className = 'list-group-item list-group-item-action';
        a.textContent = it.label; // ex: "TEBET BARAT, TEBET, JAKARTA SELATAN, DKI JAKARTA (12810)"
        a.onclick = () => {
          qInput.value = it.label;
          hidId.value  = it.id;
          hidLbl.value = it.label;
          document.getElementById('na_postal_code').value = it.postal_code || '';   // <-- penting
          listWrap.style.display = 'none';
          servicesWrap.textContent = 'Tujuan terkunci. Klik "Hitung Ongkir".';
        };
        listWrap.appendChild(a);
      });
      listWrap.style.display = 'block';
    }
    async function doSearch(q) {
      const url = `{{ route('ajax.destination.search', [], false) }}?q=${encodeURIComponent(q)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      if (!data.ok) return [];
      return (data.data || []).map(r => ({
        id: r.id,
        label: r.label,
        postal_code: r.postal_code || r.postcode || r.zip || ''
      }));
    }
    qInput?.addEventListener('input', () => {
      const q = qInput.value.trim();
      hidId.value = ''; hidLbl.value='';
      if (debTimer) clearTimeout(debTimer);
      if (q.length < 3) { listWrap.style.display = 'none'; servicesWrap.textContent = 'Ketik ≥3 huruf untuk mencari tujuan.'; return; }
      debTimer = setTimeout(async () => {
        if (q === lastQ) return;
        lastQ = q;
        listWrap.innerHTML = '<div class="list-group-item">Mencari…</div>';
        listWrap.style.display = 'block';
        try { renderResults(await doSearch(q)); }
        catch { listWrap.innerHTML = '<div class="list-group-item text-danger">Gagal mencari tujuan</div>'; }
      }, 300);
    });

    // ===== Hitung ongkir
    btnQuote.addEventListener('click', quoteNow);

    async function quoteNow(){
      resetShippingUI();

      const courier = (courierSel.value || '').trim().toLowerCase();
      if(!courier){ servicesWrap.textContent='Pilih kurir terlebih dahulu.'; return; }
      fCourier.value = courier;

      const isNew = modeNew.checked;

      if (!isNew) {
        const opt = selectSaved?.options[selectSaved.selectedIndex];
        const hasDest = opt && opt.getAttribute('data-has-dest') === '1';
        if (!hasDest) { servicesWrap.textContent='Alamat tersimpan ini belum memiliki destination_id.'; return; }
      } else {
        const destId = parseInt(hidId.value || '0', 10);
        if (!destId) { servicesWrap.textContent='Pilih tujuan dari hasil autocomplete lalu klik "Hitung Ongkir".'; return; }
      }

      try{
        servicesWrap.textContent='Menghitung…';

        let url, payload;
        if (!isNew) {
          url     = `{{ route('ajax.shipping.cost', [], false) }}`;
          payload = { address_id: selectSaved.value, courier };
        } else {
          url     = `{{ route('ajax.shipping.quote', [], false) }}`;
          payload = { destination_id: parseInt(hidId.value,10), courier };
        }

        const resp = await fetch(url, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(payload)
        });

        let data;
        try { data = await resp.json(); }
        catch{ data = { ok:false, message:'Response tidak valid.' }; }

        if(!resp.ok && data?.message){
          servicesWrap.innerHTML = `<span class="text-danger">${data.message}</span>`;
          return;
        }
        renderServices(data);
      }catch(e){
        servicesWrap.innerHTML = `<span class="text-danger">${e?.message || 'Gagal menghitung ongkir.'}</span>`;
      }
    }

    function renderServices(payload){
      servicesWrap.innerHTML='';
      if(!payload || payload.ok === false){
        servicesWrap.innerHTML = `<span class="text-danger">${payload?.message || 'Gagal menghitung ongkir.'}</span>`;
        return;
      }
      const list = payload.services || [];
      if(!list.length){ servicesWrap.textContent='Tidak ada layanan untuk rute ini.'; return; }

      list.forEach((s, i)=>{
        const price = Number(s.value?.value ?? s.value ?? s.cost ?? s.price ?? 0);
        const label = s.service ?? s.service_name ?? `SERVICE ${i+1}`;
        const etd   = s.etd ?? s.estimation ?? '-';

        const btn=document.createElement('button');
        btn.type='button';
        btn.className='btn btn-sm btn-outline-secondary me-2 mb-2';
        btn.textContent=`${label} - ${rupiah(price)} (${etd})`;
        btn.onclick=()=>{
          fService.value = label;
          fShip.value    = price;
          fEtd.value     = etd;
          uiShip.textContent  = rupiah(price);
          uiTotal.textContent = rupiah(SUBTOTAL + price);
          servicesWrap.querySelectorAll('button').forEach(b=>b.classList.remove('btn-secondary','text-white'));
          btn.classList.add('btn-secondary','text-white');
          updatePayButtonState();
        };
        servicesWrap.appendChild(btn);
      });
    }

    // Guard client-side sebelum submit
    document.getElementById('checkout-form').addEventListener('submit', function(e){
      if(!fCourier.value || !fService.value || !fShip.value){
        e.preventDefault();
        servicesWrap.innerHTML = '<span class="text-danger">Pilih layanan ongkir dulu.</span>';
        window.scrollTo({ top: servicesWrap.getBoundingClientRect().top + window.scrollY - 120, behavior: 'smooth' });
      }
    });

    // Init (browser kadang restore state)
    setMode(modeNew.checked ? 'new' : 'saved');
  });
  </script>
  @endpush
</x-app-layout>
