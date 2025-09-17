<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id','first_name','last_name','email','phone','address1','address2','postal_code',
        'subtotal','shipping','total','payment_status','order_status',
        'order_code','midtrans_order_id','midtrans_transaction_id','midtrans_payment_type','midtrans_raw'
    ];

    public function items() { return $this->hasMany(OrderItem::class); }
    public function user() { return $this->belongsTo(User::class); }
}
