<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_price',
        'product_image',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'product_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format(
            $this->subtotal,
            0,
            ',',
            '.'
        );
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format(
            $this->product_price,
            0,
            ',',
            '.'
        );
    }

    public function getProductImageUrlAttribute()
    {
        if (! $this->product_image) {
            return '';
        }

        if (Str::startsWith($this->product_image, ['http://', 'https://'])) {
            return $this->product_image;
        }

        return asset($this->product_image);
    }
}