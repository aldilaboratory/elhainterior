<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'price',
        'qty',
        'line_total',
    ];
    protected $casts = [
        'price'     => 'integer',
        'qty'       => 'integer',
        'line_total'=> 'integer',
    ];
    public function order() { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
