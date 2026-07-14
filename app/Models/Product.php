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
    'is_active',
    'is_favorite'
    ];

    protected $casts = [
    'is_active' => 'boolean',
    'price_after_tax' => 'boolean',
    'is_favorite' => 'boolean',
];

    
    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/no-image.png');
        }

        return Storage::disk('supabase')->url($this->image);
    }
}
