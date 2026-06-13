<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

public function setAttribute($key, $value)
    {
        parent::setAttribute($key, $value);

        if (in_array($key, ['is_active', 'price_after_tax'])) {
            $this->attributes[$key] = $value ? 'true' : 'false';
        }
    }
}
