<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id','is_active'];

    public function user()   { return $this->belongsTo(User::class); }
    public function items()  { return $this->hasMany(CartItem::class); }

    // total dinamis
    public function getTotalAttribute(): int {
        return (int) $this->items->sum(fn($i) => $i->qty * $i->unit_price);
    }

    // helper: tambah/merge item
    public function addItem(Product $product, int $qty): void {
        $item = $this->items()->firstOrNew(['product_id' => $product->id], [
            'unit_price' => $product->price,
        ]);
        $item->qty = ($item->exists ? $item->qty : 0) + $qty;
        // optional: clamp dengan stok
        $item->qty = max(1, min($item->qty, (int) $product->stock));
        $item->unit_price = $item->unit_price ?: $product->price; // jaga-jaga
        $item->save();
    }
}
