<?php

namespace App\Models;

use App\Concerns\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantDomain extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'tenant_domains';

    protected $fillable = [
        'tenant_id', 'organization_id', 'domain', 'normalized_domain', 'domain_type',
        'is_primary', 'verification_method', 'verification_token', 'verification_token_hash',
        'verified_at', 'dns_status', 'ssl_status', 'ssl_expires_at', 'last_checked_at',
        'failure_reason', 'type',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'verified_at' => 'datetime',
        'ssl_expires_at' => 'datetime',
        'last_checked_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
