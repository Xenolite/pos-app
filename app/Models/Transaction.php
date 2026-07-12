<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'profit',
        'service_charge',
        'payment_method',
        'payment_status',
        'midtrans_order_id',
        'snap_token',
        'payment_type',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function items()
{
    return $this->hasMany(TransactionItem::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}
}
