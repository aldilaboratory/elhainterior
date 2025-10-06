<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->user()
            ->cart()
            ->with(['items.product'])
            ->where('is_active', true)
            ->firstOrFail();

        $lines = $cart->items;

        // hitung subtotal & berat di server
        $subtotal = 0;
        $totalWeightGram = 0;
        
        foreach ($lines as $item) {
            $subtotal += $item->qty * $item->unit_price;
            $weight = (int) ($item->product->weight ?? 0);
            $totalWeightGram += $weight * $item->qty;
        }

        $shipping = 0;
        $total = $subtotal + $shipping;

        $user = $request->user();
        $addresses = $user->addresses()->latest()->get();
        $defaultAddressId = optional($user->addresses()->where('is_default', true)->first())->id;

        return view('customer.checkout.index', compact(
            'lines', 'subtotal', 'shipping', 'total', 'addresses', 'defaultAddressId', 'totalWeightGram'
        ));
    }

    public function store(Request $request)
    {
        $mode = $request->string('addr_mode'); // 'saved' | 'new'

        if ($mode === 'saved') {
            $request->validate([
                'shipping_address_id' => 'required|integer|exists:addresses,id',
            ]);
        } else { // new
            $request->validate([
                'na_label'          => 'required|string|max:50',
                'na_recipient'      => 'required|string|max:255',
                'na_phone'          => 'required|string|max:30',
                'na_address'        => 'required|string|max:255',
                'na_destination_id' => 'required|integer|min:1',
                'na_destination_label' => 'required|string|max:255',
            ]);
        }

        // wajib sudah memilih salah satu layanan
        $request->validate([
            'courier_code'    => 'required|string',
            'courier_service' => 'required|string',
            'shipping'        => 'required|integer|min:0',
            'shipping_etd'    => 'nullable|string',
        ]);

        $user = $request->user();

        // ambil/siapkan Address
        $address = null;
        if ($mode === 'saved') {
            $address = Address::where('id', $request->integer('shipping_address_id'))
                ->where('user_id', $user->id)->firstOrFail();
        }

        // hitung ulang subtotal & berat
        $cart = $user->cart()->with('items.product')->where('is_active', true)->firstOrFail();
        if ($cart->items->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang kosong.'])->withInput();
        }
        $subtotal = 0; $weight = 0;
        foreach ($cart->items as $ci) {
            $subtotal += $ci->qty * $ci->unit_price;
            $weight   += max(0, (int)($ci->product->weight ?? 0)) * (int)$ci->qty;
        }
        $weight = max(1, $weight);
        $shipping = (int)$request->input('shipping');
        $total    = $subtotal + $shipping;

        $orderCode = 'INV-'.now()->format('Ymd').'-'.str_pad((string)random_int(1,9999), 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $user, $mode, &$address, $subtotal, $weight, $shipping, $total, $cart, $orderCode) {
            // jika NEW → buat Address baru dulu
            if ($mode === 'new') {
                $makeDefault = $request->boolean('na_is_default');
                if ($makeDefault) {
                    Address::where('user_id', $user->id)->update(['is_default' => false]);
                }
                $address = Address::create([
                    'user_id'           => $user->id,
                    'label'             => $request->string('na_label'),
                    'recipient_name'    => $request->string('na_recipient'),
                    'phone'             => $request->string('na_phone'),
                    'address_line'      => $request->string('na_address'),
                    'destination_id'    => $request->integer('na_destination_id'),
                    'destination_label' => $request->string('na_destination_label'),
                    'is_default'        => $makeDefault,
                ]);
            }

            /** @var \App\Models\Order $order */
            $order = Order::create([
                'user_id'      => $user->id,
                'order_code'   => $orderCode,
                'first_name'   => $user->name,
                'email'        => $user->email,
                'phone'        => $user->phone,
                'address1'     => $address->address_line,
                'postal_code'  => $address->postal_code,
                'destination_id'       => $address->destination_id,
                'ship_to_region_label' => $address->destination_label,
                'subtotal'     => $subtotal,
                'weight_total_gram' => $weight,
                'shipping'     => $shipping,
                'courier_code' => $request->string('courier_code'),
                'courier_service' => $request->string('courier_service'),
                'shipping_etd'  => $request->string('shipping_etd'),
                'total'        => $total,
                'payment_status' => 'pending',
                'order_status'   => 'unconfirmed',
            ]);

            foreach ($cart->items as $ci) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $ci->product_id,
                    'name'       => $ci->product->name,
                    'price'      => $ci->unit_price,
                    'qty'        => $ci->qty,
                    'line_total' => $ci->qty * $ci->unit_price,
                ]);
                $ci->product->decrement('stock', (int)$ci->qty);
            }

            $cart->items()->delete();
            $cart->update(['is_active' => false]);
        });

        return redirect()->route('customer.thankyou', ['code' => $orderCode])
            ->with('success', 'Pesanan dibuat. Silakan lanjut pembayaran.');
    }
}
