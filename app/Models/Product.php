<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id','subcategory_id','name','slug','description',
        'price','stock','image_path','weight',
    ];

    protected $casts = [
        // …
    ];

    public function images()
    {
        // urutkan konsisten
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function primaryImage()
    {
        // foto utama (thumbnail)
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function category()    { return $this->belongsTo(Category::class); }
    public function subcategory() { return $this->belongsTo(Subcategory::class); }
    public function orderItems(){ return $this->hasMany(\App\Models\OrderItem::class); }

    // Akses kolom lama (jika masih dipakai)
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }

    // URL foto utama (thumbnail) + fallback
    public function getThumbnailUrlAttribute(): ?string
    {
        // 1) pakai relasi yang sudah di-eager load bila ada
        $img = $this->relationLoaded('primaryImage')
            ? $this->getRelation('primaryImage')
            : $this->primaryImage()->first();

        // 2) fallback: ambil foto pertama menurut urutan
        if (!$img) {
            $img = $this->relationLoaded('images')
                ? $this->images->first()
                : $this->images()->first();
        }

        if ($img) {
            return asset('storage/'.$img->path);
        }

        // 3) fallback terakhir: kolom image_path
        return $this->image_url; // akan null jika kosong
    }

    // (opsional) untuk kompatibilitas nama lama
    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->thumbnail_url;
    }
}