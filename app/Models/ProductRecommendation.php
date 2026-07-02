<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'recommended_product_id',
        'similarity_score',
        'computed_at',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'similarity_score' => 'decimal:4',
            'computed_at' => 'datetime',
        ];
    }

    // Produk asal
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Produk rekomendasi
    public function recommendedProduct()
    {
        return $this->belongsTo(
            Product::class,
            'recommended_product_id'
        );
    }
}