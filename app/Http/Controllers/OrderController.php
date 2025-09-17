<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = \App\Models\Order::latest()->paginate(20);
        return view('admin.orders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'nullable|string|max:100',
            'email'       => 'required|email',
            'phone'       => 'required|string|max:30',
            'address1'    => 'required|string|max:255',
            'address2'    => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'subtotal'    => 'required|integer|min:0',
            'shipping'    => 'required|integer|min:0',
            'total'       => 'required|integer|min:0',
        ]);

        $userId = auth()->id();

        // Re-calc server side
        $items = CartItem::with('product')->where('user_id',$userId)->get();
        abort_if($items->isEmpty(), 400, 'Keranjang kosong.');

        $subtotalServer = $items->sum(fn($ci) => $ci->qty * $ci->unit_price);
        $shippingServer = 25000; // statis
        $totalServer    = $subtotalServer + $shippingServer;

        // Generate kode order sederhana
        $orderCode = 'INV-'.now()->format('Ymd').'-'.str_pad((string)random_int(1,9999),4,'0',STR_PAD_LEFT);

        DB::transaction(function () use ($payload, $items, $subtotalServer, $shippingServer, $totalServer, $orderCode, $userId) {
            $order = Order::create([
                'user_id'       => $userId,
                'order_code'    => $orderCode,
                // snapshot alamat
                'first_name'    => $payload['first_name'],
                'last_name'     => $payload['last_name'] ?? null,
                'email'         => $payload['email'],
                'phone'         => $payload['phone'],
                'address1'      => $payload['address1'],
                'address2'      => $payload['address2'] ?? null,
                'postal_code'   => $payload['postal_code'],
                // ringkasan biaya
                'subtotal'      => $subtotalServer,
                'shipping'      => $shippingServer,
                'total'         => $totalServer,
                // status awal
                'payment_status'=> 'pending',     // menunggu pembayaran
                'order_status'  => 'unconfirmed', // menunggu konfirmasi
            ]);

            foreach ($items as $ci) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $ci->product_id,
                    'product_name' => $ci->product->name,
                    'unit_price'   => $ci->unit_price,
                    'qty'          => $ci->qty,
                    'line_total'   => $ci->qty * $ci->unit_price,
                ]);
            }

            // Kosongkan cart (opsional)
            CartItem::where('user_id', $userId)->delete();
        });

        // Redirect ke halaman "menunggu pembayaran" / "terima kasih"
        return redirect()->route('customer.thankyou', ['code' => $orderCode])
            ->with('success', 'Pesanan dibuat. Silakan selesaikan pembayaran.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
