<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entitlement extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'key', 'value', 'source'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
