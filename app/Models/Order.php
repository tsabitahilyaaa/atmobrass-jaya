<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'status',
        'payment_method',
        'payment_amount',
        'total_amount',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_city',
        'shipping_postal',
        'shipping_address',
        'notes',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'payment_amount' => 'decimal:2',
            'ordered_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format(
            $this->total_amount,
            0,
            ',',
            '.'
        );
    }

    public function getFormattedDateAttribute()
    {
        return $this->ordered_at
            ? $this->ordered_at->locale('id_ID')->translatedFormat('d F Y, H:i')
            : null;
    }
}