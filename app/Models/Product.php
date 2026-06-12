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
}
