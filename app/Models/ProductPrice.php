<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'billing_provider', 'provider_price_id', 'currency',
        'unit_amount', 'billing_interval', 'interval_count', 'pricing_model',
        'is_active',
    ];

    protected $casts = [
        'unit_amount' => 'integer',
        'interval_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
