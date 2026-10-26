<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainVerificationToken extends Model
{
    use HasFactory;

    protected $table = 'domain_verification_tokens';

    protected $fillable = ['tenant_domain_id', 'token_hash', 'expires_at', 'used_at'];

    protected $casts = ['expires_at' => 'datetime', 'used_at' => 'datetime'];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(TenantDomain::class, 'tenant_domain_id');
    }
}
