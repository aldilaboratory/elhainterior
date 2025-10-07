<x-app-layout>

<div class="my-5 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0">
                        <h4 class="mb-0 text-center">{{ __('Register') }}</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Name -->
                            <div class="mb-3">
                                <x-input-label for="name" :value="__('Nama')" />
                                <x-text-input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Email Address -->
                            <div class="mb-3">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- No. HP -->
                            <div class="mb-3">
                                <x-input-label for="phone" :value="__('No. HP')" />
                                <x-text-input id="phone" class="form-control" type="phone" name="phone" :value="old('phone')" required autocomplete="phone" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <!-- No. HP -->
                            <div class="mb-3">
                                <x-input-label for="address" :value="__('Alamat')" />
                                <x-text-input id="address" class="form-control" type="address" name="address" :value="old('address')" required autocomplete="address" />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            {{-- Tujuan (autocomplete RajaOngkir/Komerce) --}}
                            <div class="mb-3 position-relative">
                                <x-input-label for="dest_search" :value="__('Tujuan (Kota/Kecamatan)')" />
                                <input id="dest_search" type="text" class="form-control"
                                        name="dest_search" value="{{ old('dest_search') }}"
                                        placeholder="Cari kota/kecamatan…">

                                {{-- Hidden values to be submitted --}}
                                <input type="hidden" id="destination_id" name="destination_id" value="{{ old('destination_id') }}">
                                <input type="hidden" id="destination_label" name="destination_label" value="{{ old('destination_label') }}">
                                <input type="hidden" id="postal_code" name="postal_code" value="{{ old('postal_code') }}">

                                {{-- Hints --}}
                                <small class="text-muted d-block mt-1" id="dest_hint">
                                    @if(old('destination_label'))
                                    Terpilih: {{ old('destination_label') }} ({{ old('postal_code') }})
                                    @else
                                    Ketik minimal 3 huruf lalu pilih dari daftar.
                                    @endif
                                </small>

                                {{-- Dropdown hasil --}}
                                <div id="dest_results"
                                    class="list-group position-absolute w-100 shadow"
                                    style="z-index: 1000; display:none; max-height: 260px; overflow:auto;">
                                    {{-- items will be injected --}}
                                </div>

                                <x-input-error :messages="$errors->get('destination_id')" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <x-input-label for="password" :value="__('Password')" />

                                <x-text-input id="password" class="form-control"
                                                type="password"
                                                name="password"
                                                required autocomplete="new-password" />

                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                                <x-text-input id="password_confirmation" class="form-control"
                                                type="password"
                                                name="password_confirmation" required autocomplete="new-password" />

                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <div class="mb-3" style="display: flex; justify-content: space-between;">
                                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                                    {{ __('Sudah punya akun?') }}
                                </a>

                                <button type="submit" class="btn btn-dark">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
  const $q     = document.getElementById('dest_search');
  const $drop  = document.getElementById('dest_results');
  const $hint  = document.getElementById('dest_hint');
  const $id    = document.getElementById('destination_id');
  const $label = document.getElementById('destination_label');
  const $zip   = document.getElementById('postal_code');

  let t=null, lastQ='';

  function hide(){ $drop.style.display='none'; $drop.innerHTML=''; }
  function show(){ $drop.style.display='block'; }

  function pick(item){
    $q.value    = item.label;
    $id.value   = item.id;
    $label.value= item.label;
    $zip.value  = item.postal_code || '';
    $hint.textContent = `Terpilih: ${item.label}${item.postal_code ? ' ('+item.postal_code+')':''}`;
    hide();
  }

  // === BACA { ok, data } ATAU array langsung ===
  async function fetchRows(q){
    const res  = await fetch(`{{ route('ajax.destination.search', [], false) }}?q=`+encodeURIComponent(q), {
      headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }
    });
    const data = await res.json();
    const rows = Array.isArray(data) ? data : (Array.isArray(data?.data) ? data.data : []);
    return rows.map(r => ({ id:r.id, label:r.label, postal_code: r.postal_code || r.postcode || r.zip || '' }));
  }

  async function search(q){
    if(q.length < 3){ hide(); return; }
    if(q === lastQ) return; lastQ = q;

    $drop.innerHTML = '<div class="list-group-item">Mencari…</div>';
    show();

    try{
      const rows = await fetchRows(q);
      if(!rows.length){ hide(); return; }

      $drop.innerHTML = '';
      rows.forEach(it=>{
        const a = document.createElement('a');
        a.href  = '#';
        a.className = 'list-group-item list-group-item-action';
        a.innerHTML = `
          <div class="d-flex justify-content-between">
            <span>${it.label}</span>
            ${it.postal_code ? `<small class="text-muted">${it.postal_code}</small>`:''}
          </div>`;
        a.addEventListener('click',(e)=>{ e.preventDefault(); pick(it); });
        $drop.appendChild(a);
      });
      show();
    }catch(e){ console.error(e); hide(); }
  }

  $q.addEventListener('input', function(){
    clearTimeout(t);
    const val = this.value.trim();
    t = setTimeout(()=>search(val), 250);
    if(val.length < 3){
      $id.value=''; $label.value=''; $zip.value='';
      $hint.textContent = 'Ketik minimal 3 huruf lalu pilih dari daftar.';
    }
  });

  document.addEventListener('click', function(e){
    if(! $drop.contains(e.target) && e.target!==$q){ hide(); }
  });
})();
</script>
</x-app-layout>
