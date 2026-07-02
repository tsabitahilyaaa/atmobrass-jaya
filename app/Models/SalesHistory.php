<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_date',
        'total_quantity',
        'total_revenue',
        'total_orders',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'total_revenue' => 'decimal:2',
        ];
    }
}