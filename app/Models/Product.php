<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
    'buy_price',
    'price',
    'stock',
    'image',
    'price_after_tax',
    'is_active'
    ];

    protected $casts = [
    'is_active' => 'boolean',
    'price_after_tax' => 'boolean',
];

    /**
     * Full public URL of the product image, served from Supabase Storage.
     * Centralizing this here means views never build the storage path
     * manually (asset('storage/'.$product->image)) — if the disk or
     * folder structure ever changes, it only needs to be updated once.
     */
    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/no-image.png');
        }

        return Storage::disk('supabase')->url($this->image);
    }
}
