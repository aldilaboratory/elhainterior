<?php
// app/Http/Controllers/AddressController.php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
  public function index() {
    $addresses = auth()->user()->addresses()->latest()->get();
    return view('account.addresses.index', compact('addresses'));
  }

  public function store(Request $request) {
    // SEMUA field (kecuali checkbox is_default) wajib
    Validator::make($request->all(), [
      'label'          => ['required','string','max:50'],
      'recipient_name' => ['required','string','max:255'],
      'phone'          => ['required','string','max:30'],
      'address_line'   => ['required','string','max:255'],
      'province'       => ['required','string','max:100'],
      'city'           => ['required','string','max:100'],
      'district'       => ['required','string','max:100'],
      'village'        => ['required','string','max:100'],
      'postal_code'    => ['required','string','max:10'],
      'is_default'     => ['sometimes','boolean'],
    ])->validateWithBag('createAddress'); // <- named error bag khusus modal create

    $data = $request->only([
      'label','recipient_name','phone','address_line',
      'province','city','district','village','postal_code','is_default'
    ]);
    $data['user_id'] = auth()->id();

    DB::transaction(function() use ($data) {
      $makeDefault = !empty($data['is_default']);
      if ($makeDefault) {
        Address::where('user_id', $data['user_id'])->update(['is_default' => false]);
      }
      Address::create($data + ['is_default' => $makeDefault]);
    });

    return back()->with('success','Alamat ditambahkan.');
  }

    public function update(Request $request, Address $address) {
        abort_unless($address->user_id === auth()->id(), 403);

        Validator::make($request->all(), [
        'label'          => ['required','string','max:50'],
        'recipient_name' => ['required','string','max:255'],
        'phone'          => ['required','string','max:30'],
        'address_line'   => ['required','string','max:255'],
        'province'       => ['required','string','max:100'],
        'city'           => ['required','string','max:100'],
        'district'       => ['required','string','max:100'],
        'village'        => ['required','string','max:100'],
        'postal_code'    => ['required','string','max:10'],
        'is_default'     => ['sometimes','boolean'],
        ])->validateWithBag('editAddress');

        $data = $request->only([
        'label','recipient_name','phone','address_line',
        'province','city','district','village','postal_code'
        // sengaja TIDAK ambil 'is_default' di sini agar tidak ikut '0' tersembunyi
        ]);

        $makeDefault = $request->boolean('is_default'); // true jika dicentang

        DB::transaction(function () use ($address, $data, $makeDefault) {
            if ($makeDefault) {
                // matikan default lain
                Address::where('user_id', $address->user_id)->update(['is_default' => false]);
            }

            // is_default final:
            // - jika dicentang: true
            // - jika tidak dicentang: pertahankan nilai lama (supaya default tidak hilang tanpa sengaja)
            $payload = array_merge($data, [
                'is_default' => $makeDefault ? true : (bool) $address->is_default,
            ]);

            $address->update($payload);
        });

        return back()->with('success','Alamat diperbarui.');
    }

  public function destroy(Address $address) {
    abort_unless($address->user_id === auth()->id(), 403);
    $address->delete();
    return back()->with('success','Alamat dihapus.');
  }

  public function makeDefault(Address $address) {
    abort_unless($address->user_id === auth()->id(), 403);

    DB::transaction(function() use ($address) {
      Address::where('user_id', $address->user_id)->update(['is_default' => false]);
      $address->update(['is_default' => true]);
    });

    return back()->with('success','Alamat utama diubah.');
  }
}
