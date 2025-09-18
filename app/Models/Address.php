<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
  protected $fillable = [
    'user_id','label','recipient_name','phone',
    'address_line','province','city','district','village','postal_code',
    'lat','lng','is_default',
  ];

  public function user() {
    return $this->belongsTo(User::class);
  }
}
