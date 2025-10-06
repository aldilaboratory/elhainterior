<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->latest()->get();
        return view('account.addresses.index', compact('addresses'));
    }

    /** AJAX autocomplete tujuan Komerce/RajaOngkir V2 */
    public function searchDest(Request $request, RajaOngkirService $svc)
    {
        $q = trim((string) $request->query('q', ''));
        
        if ($q === '') {
            return response()->json(['ok' => true, 'data' => []]);
        }

        try {
            Log::info('Searching destination', [
                'query' => $q,
            ]);
            
            $rows = $svc->search($q);
            
            Log::info('Search results', [
                'count' => count($rows),
                'results' => $rows,
            ]);
            
            // TAMBAHKAN INI - return JSON response
            return response()->json(['ok' => true, 'data' => $rows]);
            
        } catch (\Throwable $e) {
            Log::error('Search failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'ok' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /** Simpan alamat (dipakai AJAX di checkout) */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'label'             => ['required','string','max:50'],
            'recipient_name'    => ['required','string','max:255'],
            'phone'             => ['required','string','max:30'],
            'address_line'      => ['required','string','max:255'],
            // Field sederhana boleh kosong, kita isi string kosong default
            'province'          => ['nullable','string','max:100'],
            'city'              => ['nullable','string','max:100'],
            'district'          => ['nullable','string','max:100'],
            'village'           => ['nullable','string','max:100'],
            'postal_code'       => ['nullable','string','max:10'],
            'is_default'        => ['sometimes','boolean'],
            // WAJIB untuk ongkir
            'destination_id'    => ['required','integer','min:1'],
            'destination_label' => ['required','string','max:255'],
        ])->validate();

        $data = $request->only([
            'label','recipient_name','phone','address_line',
            'province','city','district','village','postal_code','is_default',
            'destination_id','destination_label',
        ]);
        $data['user_id'] = auth()->id();

        $created = null;
        DB::transaction(function () use ($data, &$created) {
            $makeDefault = !empty($data['is_default']);
            if ($makeDefault) {
                Address::where('user_id', $data['user_id'])->update(['is_default' => false]);
            }
            $created = Address::create($data + ['is_default' => $makeDefault]);
        });

        if ($request->expectsJson()) {
            return response()->json(['ok'=>true,'id'=>$created->id]);
        }

        return back()->with('success','Alamat ditambahkan.');
    }

    public function update(Request $request, Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);

        Validator::make($request->all(), [
            'label'             => ['required','string','max:50'],
            'recipient_name'    => ['required','string','max:255'],
            'phone'             => ['required','string','max:30'],
            'address_line'      => ['required','string','max:255'],
            'province'          => ['nullable','string','max:100'],
            'city'              => ['nullable','string','max:100'],
            'district'          => ['nullable','string','max:100'],
            'village'           => ['nullable','string','max:100'],
            'postal_code'       => ['nullable','string','max:10'],
            'is_default'        => ['sometimes','boolean'],
            // tetap wajib agar ongkir aman
            'destination_id'    => ['required','integer','min:1'],
            'destination_label' => ['required','string','max:255'],
        ])->validate();

        $payload = $request->only([
            'label','recipient_name','phone','address_line',
            'province','city','district','village','postal_code',
            'destination_id','destination_label',
        ]);

        $makeDefault = $request->boolean('is_default');

        DB::transaction(function () use ($address, $payload, $makeDefault) {
            if ($makeDefault) {
                Address::where('user_id', $address->user_id)->update(['is_default' => false]);
            }
            $address->update($payload + [
                'is_default' => $makeDefault ? true : (bool) $address->is_default,
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json(['ok'=>true]);
        }

        return back()->with('success','Alamat diperbarui.');
    }

    public function destroy(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $address->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok'=>true]);
        }
        return back()->with('success','Alamat dihapus.');
    }

    public function makeDefault(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);

        DB::transaction(function () use ($address) {
            Address::where('user_id', $address->user_id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        if (request()->expectsJson()) {
            return response()->json(['ok'=>true]);
        }
        return back()->with('success','Alamat utama diubah.');
    }
}
