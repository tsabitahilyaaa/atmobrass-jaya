<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'total', 'status', 'payment_method',
        'shipping_name', 'shipping_phone', 'shipping_city', 'shipping_address', 'shipping_postal',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->locale('id_ID')->translatedFormat('d F Y, H:i');
    }
}