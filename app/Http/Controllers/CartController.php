<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = auth()->check()
            ? Cart::with(['items.product.primaryImage'])
                ->where('user_id', auth()->id())
                ->where('is_active', true)
                ->first()
            : null;

        $lines = $cart?->items ?? collect();
        $subtotal = $lines->sum(fn($it) => (int)$it->qty * (int)$it->unit_price);

        return view('customer.cart', compact('cart','lines','subtotal'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'qty'        => ['required','integer','min:1'],
        ]);

        if (!auth()->check()) {
            // simpan niat add-to-cart
            $pending = session('pending_cart', []);
            $pending[] = $data; // ['product_id'=>..,'qty'=>..]
            session(['pending_cart' => $pending]);

            return redirect()->guest(route('login'));
        }

        $product = Product::findOrFail($data['product_id']);
        if ($product->stock <= 0) {
            return back()->withErrors('Stok habis.');
        }

        $cart = Cart::firstOrCreate(['user_id' => auth()->id(), 'is_active' => true]);
        $cart->load('items');
        $cart->addItem($product, $data['qty']);

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function updateItem(Request $request, CartItem $item)
    {
        $this->authorizeOwner($item->cart_id);

        $data = $request->validate(['qty' => ['required','integer','min:1']]);
        $max  = (int) $item->product->stock;
        $qty  = max(1, min($data['qty'], $max ?: $data['qty']));

        $item->qty = $qty;
        $item->save();

        // hitung ulang subtotal cart user ini
        $cart = Cart::with('items')->findOrFail($item->cart_id);
        $subtotal = $cart->items->sum(fn($it) => (int)$it->qty * (int)$it->unit_price);

        if ($request->wantsJson()) {
            return response()->json([
                'ok'         => true,
                'qty'        => $item->qty,
                'stock'      => $max,
                'line_total' => (int)$item->qty * (int)$item->unit_price,
                'subtotal'   => (int)$subtotal,
            ]);
        }

        return back()->with('success','Kuantitas diperbarui.');
    }

    public function removeItem(CartItem $item)
    {
        $this->authorizeOwner($item->cart_id);
        $item->delete();

        return back()->with('success','Item dihapus.');
    }

    private function authorizeOwner(int $cartId): void
    {
        $owned = Cart::where('id',$cartId)
            ->where('user_id', auth()->id())->exists();
        abort_unless($owned, 403);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }
}