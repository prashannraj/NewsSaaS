<?php

namespace App\Models;

use App\Concerns\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantSocialLink extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = ['tenant_id', 'organization_id', 'platform', 'url', 'sort_order'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
