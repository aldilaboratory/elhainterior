<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
  // app/Models/Address.php
  protected $fillable = [
    'user_id','label','recipient_name','phone','address_line',
    'province','city','district','village','postal_code',
    'lat','lng','is_default',
    'destination_id','destination_label', // V2
  ];

  protected $casts = [
        'is_default' => 'boolean',
    ];

  public function user() {
    return $this->belongsTo(User::class);
  }
}
