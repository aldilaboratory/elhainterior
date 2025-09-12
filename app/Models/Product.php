<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
    'category_id','subcategory_id','name','slug','description',
    'price','stock','image_path',
    ];

    protected $casts = [
        //
    ];

    public function category()    { 
        return $this->belongsTo(Category::class); 
    }
    public function subcategory() { 
        return $this->belongsTo(Subcategory::class); 
    }

    // helper
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
