<section>
  <header class="mb-3">
    <h2 class="text-lg font-medium text-gray-900">Informasi Alamat</h2>
    <p class="mt-1 text-sm text-gray-600">Kelola alamat untuk pengiriman.</p>
  </header>

  {{-- Header actions --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <button type="button" class="btn btn-dark btn-sm"
            data-toggle="modal" data-target="#modalCreateAddress">
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
              {{ $a->village ? $a->village.', ' : '' }}{{ $a->district ? $a->district.', ' : '' }}{{ $a->city ? $a->city.', ' : '' }}{{ $a->province ?: '' }} {{ $a->postal_code ?: '' }}
            </div>
          </div>

          <div class="text-nowrap">
            {{-- Jadikan Default --}}
            @unless($a->is_default)
              <form method="POST" action="{{ route('addresses.make-default', $a) }}" class="d-inline">
                @csrf
                @method('PATCH')
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
                    data-province="{{ $a->province }}"
                    data-city="{{ $a->city }}"
                    data-district="{{ $a->district }}"
                    data-village="{{ $a->village }}"
                    data-postal_code="{{ $a->postal_code }}"
                    data-is_default="{{ $a->is_default ? 1 : 0 }}">
                Edit
            </button>
            
            <form id="delete-form-{{ $a->id }}" action="{{ route('addresses.destroy', $a) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
                <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $a->id }}" data-name="{{ $a->recipient_name }}"><i class="mdi mdi-delete align-middle"></i> Hapus</button>
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
        <div class="modal-header">
          <h5 class="modal-title">Tambah Alamat</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
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
              <label>Alamat <span class="text-danger">*</span></label>
              <input name="address_line" class="form-control @error('address_line','createAddress') is-invalid @enderror"
                     value="{{ old('address_line') }}" required placeholder="Jalan, RT/RW, patokan">
              @error('address_line','createAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group col-md-4">
              <label>Provinsi <span class="text-danger">*</span></label>
              <input name="province" class="form-control @error('province','createAddress') is-invalid @enderror"
                     value="{{ old('province') }}" required>
              @error('province','createAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group col-md-4">
              <label>Kota/Kabupaten <span class="text-danger">*</span></label>
              <input name="city" class="form-control @error('city','createAddress') is-invalid @enderror"
                     value="{{ old('city') }}" required>
              @error('city','createAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group col-md-4">
              <label>Kecamatan <span class="text-danger">*</span></label>
              <input name="district" class="form-control @error('district','createAddress') is-invalid @enderror"
                     value="{{ old('district') }}" required>
              @error('district','createAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group col-md-6">
              <label>Kelurahan/Desa <span class="text-danger">*</span></label>
              <input name="village" class="form-control @error('village','createAddress') is-invalid @enderror"
                     value="{{ old('village') }}" required>
              @error('village','createAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group col-md-6">
              <label>Kode Pos <span class="text-danger">*</span></label>
              <input name="postal_code" class="form-control @error('postal_code','createAddress') is-invalid @enderror"
                     value="{{ old('postal_code') }}" required>
              @error('postal_code','createAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group col-12 form-check mt-2">
              <input class="form-check-input" type="checkbox" name="is_default" id="create_is_default" value="1"
                     {{ old('is_default') ? 'checked' : '' }}>
              <label for="create_is_default" class="form-check-label">Jadikan sebagai alamat utama</label>
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

{{-- Modal Edit Alamat (Bootstrap 4, styling sama dengan Tambah) --}}
<div class="modal fade" id="editAddressModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <form class="modal-content" method="POST" action="">
      @csrf
      @method('PATCH')

      <div class="modal-body p-5">
        <h5 class="modal-title text-start mb-3">Edit Alamat</h5>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label>Label <span class="text-danger">*</span></label>
            <input name="label"
                   id="edit-label"
                   class="form-control @error('label','editAddress') is-invalid @enderror"
                   value="{{ old('label') }}"
                   required
                   placeholder="Rumah / Kantor">
            @error('label','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group col-md-8">
            <label>Nama Penerima <span class="text-danger">*</span></label>
            <input name="recipient_name"
                   id="edit-recipient_name"
                   class="form-control @error('recipient_name','editAddress') is-invalid @enderror"
                   value="{{ old('recipient_name') }}"
                   required>
            @error('recipient_name','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group col-md-12">
            <label>No. HP <span class="text-danger">*</span></label>
            <input name="phone"
                   id="edit-phone"
                   class="form-control @error('phone','editAddress') is-invalid @enderror"
                   value="{{ old('phone') }}"
                   required>
            @error('phone','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group col-12">
            <label>Alamat <span class="text-danger">*</span></label>
            <input name="address_line"
                   id="edit-address_line"
                   class="form-control @error('address_line','editAddress') is-invalid @enderror"
                   value="{{ old('address_line') }}"
                   required
                   placeholder="Jalan, RT/RW, patokan">
            @error('address_line','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group col-md-4">
            <label>Provinsi <span class="text-danger">*</span></label>
            <input name="province"
                   id="edit-province"
                   class="form-control @error('province','editAddress') is-invalid @enderror"
                   value="{{ old('province') }}"
                   required>
            @error('province','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group col-md-4">
            <label>Kota/Kabupaten <span class="text-danger">*</span></label>
            <input name="city"
                   id="edit-city"
                   class="form-control @error('city','editAddress') is-invalid @enderror"
                   value="{{ old('city') }}"
                   required>
            @error('city','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group col-md-4">
            <label>Kecamatan <span class="text-danger">*</span></label>
            <input name="district"
                   id="edit-district"
                   class="form-control @error('district','editAddress') is-invalid @enderror"
                   value="{{ old('district') }}"
                   required>
            @error('district','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group col-md-6">
            <label>Kelurahan/Desa <span class="text-danger">*</span></label>
            <input name="village"
                   id="edit-village"
                   class="form-control @error('village','editAddress') is-invalid @enderror"
                   value="{{ old('village') }}"
                   required>
            @error('village','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group col-md-6">
            <label>Kode Pos <span class="text-danger">*</span></label>
            <input name="postal_code"
                   id="edit-postal_code"
                   class="form-control @error('postal_code','editAddress') is-invalid @enderror"
                   value="{{ old('postal_code') }}"
                   required>
            @error('postal_code','editAddress')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group col-12 form-check mt-2">
            <input class="form-check-input" type="checkbox" name="is_default" id="edit-is_default" value="1"
                   {{ old('is_default') ? 'checked' : '' }}>
            <label for="edit-is_default" class="form-check-label">Jadikan sebagai alamat utama</label>
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
    @if ($errors->hasBag('createAddress'))
      $('#modalCreateAddress').modal('show');
    @endif
    @if ($errors->hasBag('editAddress'))
      $('#modalEditAddress').modal('show');
    @endif
  });
  </script>

  <script>
document.addEventListener('DOMContentLoaded', function () {
  // Prefill saat modal dibuka
  $('#editAddressModal').on('show.bs.modal', function (event) {
    var btn = $(event.relatedTarget);
    if (!btn.length) return;

    var id = btn.data('id');

    // set action form
    $(this).find('form').attr('action', '{{ route('addresses.update','__ID__') }}'.replace('__ID__', id));

    // isi nilai
    $('#edit-label').val(btn.data('label') || '');
    $('#edit-recipient_name').val(btn.data('recipient_name') || '');
    $('#edit-phone').val(btn.data('phone') || '');
    $('#edit-address_line').val(btn.data('address_line') || '');
    $('#edit-province').val(btn.data('province') || '');
    $('#edit-city').val(btn.data('city') || '');
    $('#edit-district').val(btn.data('district') || '');
    $('#edit-village').val(btn.data('village') || '');
    $('#edit-postal_code').val(btn.data('postal_code') || '');
    $('#edit-is_default').prop('checked', btn.data('is_default') == 1);

    setTimeout(function(){ $('#edit-recipient_name').trigger('focus'); }, 50);
  });

  // Auto-show kalau validasi edit gagal
  @if ($errors->hasBag('editAddress'))
    $('#editAddressModal').modal('show');
  @endif
});
</script>
</section>
