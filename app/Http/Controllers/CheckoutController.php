<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Ambil cart aktif (silakan sesuaikan dengan implementasi cart kamu)
        // Contoh asumsi relasi: auth()->user()->cart->items()->with('product')
        $cart = auth()->user()->cart()->with(['items.product'])->firstOrFail();

        $lines = $cart->items;
        $subtotal = (int) $cart->items()
            ->select(DB::raw('SUM(qty * unit_price) as s'))
            ->value('s') ?? 0;

        // Ongkir statis
        $shipping = 25000; // Rp25.000
        $total    = $subtotal + $shipping;
        
        $user = auth()->user();
        $addresses = $user->addresses()->get();
        $defaultAddressId = optional($user->defaultAddress)->id;

        return view('customer.checkout.index', compact('lines', 'subtotal', 'shipping', 'total','addresses','defaultAddressId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'email'        => 'required|email',
            'phone'        => 'required|string|max:30',
            'address1'     => 'required|string|max:255',
            'address2'     => 'nullable|string|max:255',
            'postal_code'  => 'required|string|max:20',
            'subtotal'     => 'required|integer|min:0',
            'shipping'     => 'required|integer|min:0',
            'total'        => 'required|integer|min:0',
        ]);

        $user = $request->user();

        // Ambil cart aktif
        $cart = $user->cart()->with(['items.product'])->firstOrFail();
        abort_if($cart->items->isEmpty(), 400, 'Keranjang kosong.');

        // Re-calc server side
        $subtotal = (int) $cart->items->sum(fn($ci)=> $ci->qty * $ci->unit_price);
        $shipping = 25000;
        $total    = $subtotal + $shipping;

        $orderCode = 'INV-'.now()->format('Ymd').'-'.str_pad((string)random_int(1,9999),4,'0',STR_PAD_LEFT);

        DB::transaction(function () use ($user, $data, $cart, $subtotal, $shipping, $total, $orderCode) {
            $order = \App\Models\Order::create([
                'user_id'       => $user->id,
                'order_code'    => $orderCode,
                'first_name'    => $data['first_name'],
                'last_name'     => $data['last_name'] ?? null,
                'email'         => $data['email'],
                'phone'         => $data['phone'],
                'address1'      => $data['address1'],
                'address2'      => $data['address2'] ?? null,
                'postal_code'   => $data['postal_code'],
                'subtotal'      => $subtotal,
                'shipping'      => $shipping,
                'total'         => $total,
                'payment_status'=> 'pending',
                'order_status'  => 'unconfirmed',
            ]);

            foreach ($cart->items as $ci) {
                \App\Models\OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $ci->product_id,
                    'name' => $ci->product->name,
                    'price'   => $ci->unit_price,
                    'qty'          => $ci->qty,
                    'line_total'   => $ci->qty * $ci->unit_price,
                ]);

                // optional kurangi stok
                $ci->product->decrement('stock', $ci->qty);
            }

            // kosongkan cart
            $cart->items()->delete();
            $cart->update(['is_active' => false]);
        });

        return redirect()->route('customer.thankyou', ['code' => $orderCode])
            ->with('success', 'Pesanan dibuat. Silakan lanjut pembayaran.');
    }
}
