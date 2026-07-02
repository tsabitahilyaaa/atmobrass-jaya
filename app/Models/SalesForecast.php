<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'forecast_date',
        'predicted_quantity',
        'predicted_revenue',
        'confidence_score',
        'model_version',
        'computed_at',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'forecast_date' => 'date',
            'computed_at' => 'datetime',
            'predicted_revenue' => 'decimal:2',
            'confidence_score' => 'decimal:2',
        ];
    }
}