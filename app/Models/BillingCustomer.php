<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'billing_provider', 'provider_customer_id', 'email', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
