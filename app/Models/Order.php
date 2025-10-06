<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // app/Models/Order.php
    protected $casts = [
        'midtrans_raw' => 'array',
        'snap_token_expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];
    protected $fillable = [
        'user_id','order_code','first_name','last_name','email','phone',
        'address1','address2','postal_code',
        'destination_id','ship_to_region_label',
        'subtotal','weight_total_gram','shipping',
        'courier_code','courier_service','shipping_etd',
        'total','payment_status','order_status',
        // midtrans
        'snap_token','snap_redirect_url','snap_token_expired_at',
        'midtrans_order_id',        
        'midtrans_transaction_id',  
        'midtrans_payment_type',    
        'midtrans_status',          
        'fraud_status',             
        // optional channel fields
        'va_number','va_bank','payment_code',
        // timestamps
        'paid_at','cancelled_at',
    ];

    public function items() { return $this->hasMany(OrderItem::class); }
    public function user() { return $this->belongsTo(User::class); }
}
