<?php

namespace App\Models;

use App\Concerns\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Advertisement extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id', 'organization_id', 'advertiser_name', 'title', 'image_id',
        'image_path', 'placement', 'type', 'content', 'url', 'active', 'status',
        'starts_at', 'ends_at', 'impressions_count', 'clicks_count',
    ];

    protected $casts = [
        'active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'impressions_count' => 'integer',
        'clicks_count' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'image_id');
    }
}
