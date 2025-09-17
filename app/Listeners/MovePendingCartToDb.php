<?php

namespace App\Listeners;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MovePendingCartToDb
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $pending = session('pending_cart', []);
        if (empty($pending)) return;

        $cart = Cart::firstOrCreate(['user_id' => $event->user->id, 'is_active' => true]);
        $cart->load('items');

        foreach ($pending as $row) {
            $pid = (int)($row['product_id'] ?? 0);
            $qty = (int)($row['qty'] ?? 0);
            if ($pid <= 0 || $qty <= 0) continue;

            $product = Product::find($pid);
            if (!$product || $product->stock <= 0) continue;

            $cart->addItem($product, $qty);
        }

        session()->forget('pending_cart');
    }
}
