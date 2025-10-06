<?php

// app/Models/ShippingOrigin.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShippingOrigin extends Model {
  protected $fillable = ['origin_id','label','address_line','postal_code','is_active'];
}
