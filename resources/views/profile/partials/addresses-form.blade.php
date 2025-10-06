<section>
  <header class="mb-3">
    <h2 class="text-lg font-medium text-gray-900">Informasi Alamat</h2>
    <p class="mt-1 text-sm text-gray-600">Kelola alamat untuk pengiriman.</p>
  </header>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#modalCreateAddress">
      + Tambah Alamat
    </button>
  </div>

  {{-- List alamat --}}
  @if($addresses->isEmpty())
    <div class="alert alert-light border">Belum ada alamat tersimpan.</div>
  @else
    <div class="list-group">
      @foreach($addresses as $a)
        <div class="list-group-item d-flex justify-content-between align-items-start">
          <div class="pr-3">
            <div class="mb-1">
              <strong>{{ $a->recipient_name }}</strong>
              @if($a->is_default)
                <span class="badge badge-dark ml-2">Default</span>
              @endif
              @if($a->label)
                <span class="badge badge-secondary ml-1">{{ $a->label }}</span>
              @endif
            </div>
            <div class="small text-muted">
              {{ $a->phone ?: '-' }}<br>
              {{ $a->address_line }}<br>
              {{-- tampilkan label tujuan bila ada --}}
              <em>{{ $a->destination_label ?: 'Tujuan belum di-set' }}</em>
            </div>
          </div>

          <div class="text-nowrap">
            @unless($a->is_default)
              <form method="POST" action="{{ route('addresses.make-default', $a) }}" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-outline-secondary btn-sm" type="submit">Jadikan Default</button>
              </form>
            @endunless

            {{-- Edit --}}
            <button type="button"
                    class="btn btn-info btn-sm text-white"
                    data-toggle="modal" data-target="#editAddressModal"
                    data-id="{{ $a->id }}"
                    data-label="{{ $a->label }}"
                    data-recipient_name="{{ $a->recipient_name }}"
                    data-phone="{{ $a->phone }}"
                    data-address_line="{{ $a->address_line }}"
                    data-destination_id="{{ $a->destination_id }}"
                    data-destination_label="{{ $a->destination_label }}"
                    data-is_default="{{ $a->is_default ? 1 : 0 }}">
              Edit
            </button>

            {{-- Hapus --}}
            <form id="delete-form-{{ $a->id }}" action="{{ route('addresses.destroy', $a) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $a->id }}" data-name="{{ $a->recipient_name }}">
                <i class="mdi mdi-delete align-middle"></i> Hapus
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @endif

  {{-- ======================= MODAL: CREATE ======================= --}}
  <div class="modal fade" id="modalCreateAddress" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <form class="modal-content" method="POST" action="{{ route('addresses.store') }}">
        @csrf
        <div class="modal-body p-5">
        <h5 class="modal-title text-start mb-3">Tambah Alamat</h5>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Label <span class="text-danger">*</span></label>
              <input name="label" class="form-control @error('label','createAddress') is-invalid @enderror"
                     value="{{ old('label') }}" required placeholder="Rumah / Kantor">
              @error('label','createAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group col-md-8">
              <label>Nama Penerima <span class="text-danger">*</span></label>
              <input name="recipient_name" class="form-control @error('recipient_name','createAddress') is-invalid @enderror"
                     value="{{ old('recipient_name', auth()->user()->name) }}" required>
              @error('recipient_name','createAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group col-md-6">
              <label>No. HP <span class="text-danger">*</span></label>
              <input name="phone" class="form-control @error('phone','createAddress') is-invalid @enderror"
                     value="{{ old('phone', auth()->user()->phone) }}" required>
              @error('phone','createAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group col-12">
              <label>Alamat (jalan/rt/rw/patokan) <span class="text-danger">*</span></label>
              <input name="address_line" class="form-control @error('address_line','createAddress') is-invalid @enderror"
                     value="{{ old('address_line') }}" required placeholder="Jl. Contoh No. 1 RT 01/02">
              @error('address_line','createAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- TUJUAN (autocomplete) --}}
            <div class="form-group col-12">
              <label>Tujuan (Komerce/RajaOngkir V2) <span class="text-danger">*</span></label>
              <input class="form-control" id="create_dest_search" placeholder="kuta / denpasar / tebet…" autocomplete="off">
              <small class="text-muted">Pilih salah satu hasil.</small>
              <div id="create_dest_results" class="list-group mt-1" style="max-height:220px; overflow:auto; display:none;"></div>
              <input type="hidden" name="destination_id" id="create_destination_id" value="{{ old('destination_id') }}">
              <input type="hidden" name="destination_label" id="create_destination_label" value="{{ old('destination_label') }}">
              @error('destination_id','createAddress')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="form-group col-12 form-check mt-2">
              <input class="form-check-input ms-1" type="checkbox" name="is_default" id="create_is_default" value="1"
                     {{ old('is_default') ? 'checked' : '' }}>
              <label for="create_is_default" class="form-check-label ms-2">Jadikan sebagai alamat utama</label>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-light" type="button" data-dismiss="modal">Batal</button>
          <button class="btn btn-dark" type="submit">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ======================= MODAL: EDIT ======================= --}}
  <div class="modal fade" id="editAddressModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <form class="modal-content" method="POST" action="">
        @csrf @method('PATCH')
        <div class="modal-body p-5">
          <h5 class="modal-title text-start mb-3">Edit Alamat</h5>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Label <span class="text-danger">*</span></label>
              <input name="label" id="edit-label" class="form-control @error('label','editAddress') is-invalid @enderror" required placeholder="Rumah / Kantor">
              @error('label','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group col-md-8">
              <label>Nama Penerima <span class="text-danger">*</span></label>
              <input name="recipient_name" id="edit-recipient_name" class="form-control @error('recipient_name','editAddress') is-invalid @enderror" required>
              @error('recipient_name','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group col-md-12">
              <label>No. HP <span class="text-danger">*</span></label>
              <input name="phone" id="edit-phone" class="form-control @error('phone','editAddress') is-invalid @enderror" required>
              @error('phone','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group col-12">
              <label>Alamat (jalan/rt/rw/patokan) <span class="text-danger">*</span></label>
              <input name="address_line" id="edit-address_line" class="form-control @error('address_line','editAddress') is-invalid @enderror" required placeholder="Jl. Contoh No. 1 RT 01/02">
              @error('address_line','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <input type="hidden" id="na_postal_code" name="na_postal_code">

            {{-- TUJUAN (autocomplete) --}}
            <div class="form-group col-12">
              <label>Tujuan (Komerce/RajaOngkir V2) <span class="text-danger">*</span></label>
              <input class="form-control" id="edit_dest_search" placeholder="kuta / denpasar / tebet…" autocomplete="off">
              <small class="text-muted">Pilih salah satu hasil.</small>
              <div id="edit_dest_results" class="list-group mt-1" style="max-height:220px; overflow:auto; display:none;"></div>
              <input type="hidden" name="destination_id" id="edit_destination_id">
              <input type="hidden" name="destination_label" id="edit_destination_label">
              @error('destination_id','editAddress')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="form-group col-12 form-check mt-2">
              <input class="form-check-input ms-1" type="checkbox" name="is_default" id="edit-is_default" value="1">
              <label for="edit-is_default" class="form-check-label ms-2">Jadikan sebagai alamat utama</label>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <button class="btn btn-dark" type="submit">Update Alamat</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Auto-open modal saat validasi gagal --}}
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    @if ($errors->hasBag('createAddress')) $('#modalCreateAddress').modal('show'); @endif
    @if ($errors->hasBag('editAddress')) $('#editAddressModal').modal('show'); @endif
  });
  </script>

  {{-- JS: Prefill Edit & Autocomplete (Create + Edit) --}}
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    // ---------- Prefill EDIT ----------
    $('#editAddressModal').on('show.bs.modal', function (event) {
      var btn = $(event.relatedTarget); if (!btn.length) return;
      var id = btn.data('id');
      $(this).find('form').attr('action', '{{ route('addresses.update','__ID__') }}'.replace('__ID__', id));

      $('#edit-label').val(btn.data('label') || '');
      $('#edit-recipient_name').val(btn.data('recipient_name') || '');
      $('#edit-phone').val(btn.data('phone') || '');
      $('#edit-address_line').val(btn.data('address_line') || '');
      $('#edit_destination_id').val(btn.data('destination_id') || '');
      $('#edit_destination_label').val(btn.data('destination_label') || '');
      $('#edit_dest_search').val(btn.data('destination_label') || '');
      $('#edit-is_default').prop('checked', btn.data('is_default') == 1);

      setTimeout(function(){ $('#edit-recipient_name').trigger('focus'); }, 50);
    });

    // ---------- Autocomplete helper ----------
    async function aoSearch(q){
      const res = await fetch(`{{ route('ajax.destination.search', [], false) }}?q=${encodeURIComponent(q)}`, {headers:{'Accept':'application/json'}});
      const data = await res.json();
      if(!data.ok) return [];
      return (data.data||[]).map(r=>({id:r.id,label:r.label}));
    }
    function wireAutocomplete(input, list, hidId, hidLbl){
      let t=null, last='';
      input.addEventListener('input', ()=>{
        const q=input.value.trim();
        hidId.value=''; hidLbl.value='';
        if(t) clearTimeout(t);
        if(q.length<3){ list.style.display='none'; return; }
        t=setTimeout(async ()=>{
          if(q===last) return; last=q;
          list.innerHTML='<div class="list-group-item">Mencari…</div>'; list.style.display='block';
          try{
            const items = await aoSearch(q);
            list.innerHTML = '';
            if(!items.length){ list.style.display='none'; return; }
            items.forEach(it=>{
              const a=document.createElement('a');
              a.href='javascript:void(0)'; a.className='list-group-item list-group-item-action';
              a.textContent=it.label;
              a.onclick=()=>{ input.value=it.label; hidId.value=it.id; hidLbl.value=it.label; list.style.display='none'; };
              list.appendChild(a);
            });
          }catch{ list.innerHTML='<div class="list-group-item text-danger">Gagal mencari tujuan</div>'; }
        },300);
      });
    }

    // Create
    wireAutocomplete(
      document.getElementById('create_dest_search'),
      document.getElementById('create_dest_results'),
      document.getElementById('create_destination_id'),
      document.getElementById('create_destination_label')
    );
    // Edit
    wireAutocomplete(
      document.getElementById('edit_dest_search'),
      document.getElementById('edit_dest_results'),
      document.getElementById('edit_destination_id'),
      document.getElementById('edit_destination_label')
    );

    // Konfirmasi hapus
    $('.delete-btn').on('click', function(){
      const id=$(this).data('id'); const name=$(this).data('name');
      if(confirm(`Hapus alamat untuk "${name}"?`)){
        document.getElementById(`delete-form-${id}`).submit();
      }
    });
  });
  </script>
</section>
