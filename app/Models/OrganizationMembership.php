<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrganizationMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'user_id', 'membership_status', 'joined_at', 'invited_by',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'organization_membership_roles', 'organization_membership_id', 'role_id');
    }
}
