<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'slug', 'name', 'description', 'type', 'billing_interval',
        'interval_count', 'pricing_model', 'price_cents', 'base_amount', 'currency',
        'trial_days', 'seat_limit', 'article_limit', 'storage_limit_bytes',
        'domain_limit', 'is_active', 'status', 'is_public', 'sort_order', 'features',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'features' => 'array',
        'metadata' => 'array',
        'price_cents' => 'integer',
        'base_amount' => 'integer',
    ];

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'plan_features', 'plan_id', 'feature_id')
            ->withPivot('value');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
