<?php

// app/Http/Controllers/ShippingController.php
namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\ShippingOrigin;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    /** POST /ajax/shipping/cost */
    public function cost(Request $req, RajaOngkirService $svc)
    {
        try {
            $data = $req->validate([
                'address_id' => 'required|integer|exists:addresses,id',
                'courier'    => 'required|string',
            ]);

            $address = Address::where('id',$data['address_id'])
                      ->where('user_id', Auth::id())->firstOrFail();

            $origin = ShippingOrigin::where('is_active', true)->first();
            if (!$origin?->origin_id) {
                return response()->json(['ok'=>false,'message'=>'Origin belum diset.'], 422);
            }
            if (!$address->destination_id) {
                return response()->json(['ok'=>false,'message'=>'Alamat belum punya destination_id.'], 422);
            }

            // hitung subtotal & berat dari cart aktif (sesuaikan relasi cart kamu)
            $cart = Auth::user()->cart()->with('items.product')->where('is_active',true)->first();
            abort_if(!$cart || $cart->items->isEmpty(), 422, 'Keranjang kosong.');
            $itemsTotal = 0; $totalWeight = 0;
            foreach ($cart->items as $it) {
                $qty = (int) $it->qty;
                $unit= (int) $it->unit_price;
                $w   = (int) ($it->product->weight ?? 0);
                $itemsTotal += $unit * $qty;
                $totalWeight += max(0,$w) * $qty;
            }
            $weight = max(1, $totalWeight); // gram

            $services = $svc->calculateDomestic(
                originId: (int) $origin->origin_id,
                destinationId: (int) $address->destination_id,
                weightGram: $weight,
                courier: $data['courier']
            );

            $out = [];
            foreach ($services as $s) {
                $value = (int) ($s['cost'] ?? $s['value'] ?? 0);
                $out[] = [
                    'service'     => $s['service'] ?? ($s['service_name'] ?? 'SERVICE'),
                    'value'       => $value,
                    'etd'         => $s['etd'] ?? ($s['estimation'] ?? null),
                    'items_total' => $itemsTotal,
                    'grand_total' => $itemsTotal + $value,
                    'weight' => $weight,
                ];
            }
            return response()->json(['ok'=>true,'services'=>$out]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['ok'=>false,'message'=>$e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('ajax-cost', ['msg'=>$e->getMessage()]);
            return response()->json(['ok'=>false,'message'=>'Gagal menghitung ongkir: '.$e->getMessage()], 500);
        }
    }

    public function quote(Request $req, RajaOngkirService $svc)
    {
        try {
            $data = $req->validate([
                'destination_id' => 'required|integer|min:1',
                'courier'        => 'required|string',
            ]);

            $origin = \App\Models\ShippingOrigin::where('is_active', true)->first();
            if (!$origin?->origin_id) {
                return response()->json(['ok'=>false,'message'=>'Origin belum diset.'], 422);
            }

            // hitung subtotal & berat dari cart aktif
            $cart = $req->user()->cart()->with('items.product')->where('is_active', true)->first();
            abort_if(!$cart || $cart->items->isEmpty(), 422, 'Keranjang kosong.');
            $itemsTotal = 0; $totalWeight = 0;
            foreach ($cart->items as $it) {
                $qty = (int) $it->qty;
                $itemsTotal += (int)$it->unit_price * $qty;
                $totalWeight += max(0, (int)($it->product->weight ?? 0)) * $qty; // gram
            }
            $weight = max(1, $totalWeight);

            $services = $svc->calculateDomestic(
                originId: (int)$origin->origin_id,
                destinationId: (int)$data['destination_id'],
                weightGram: $weight,
                courier: $data['courier']
            );

            $out = [];
            foreach ($services as $s) {
                $value = (int) ($s['cost'] ?? $s['value'] ?? 0);
                $out[] = [
                    'service'     => $s['service'] ?? ($s['service_name'] ?? 'SERVICE'),
                    'value'       => $value,
                    'etd'         => $s['etd'] ?? ($s['estimation'] ?? null),
                    'weight_gram' => $weight,
                    'grand_total' => $itemsTotal + $value,
                ];
            }
            return response()->json(['ok'=>true,'services'=>$out]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['ok'=>false,'message'=>$e->getMessage()], 422);
        } catch (\Throwable $e) {
            \Log::error('ajax-quote', ['msg'=>$e->getMessage()]);
            return response()->json(['ok'=>false,'message'=>'Gagal menghitung ongkir: '.$e->getMessage()], 500);
        }
    }
}
