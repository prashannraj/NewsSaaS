<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'tenant_key', 'subdomain', 'primary_domain_id',
        'status', 'provisioned_at', 'suspended_at', 'settings_json',
    ];

    protected $casts = [
        'provisioned_at' => 'datetime',
        'suspended_at' => 'datetime',
        'settings_json' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function primaryDomain(): BelongsTo
    {
        return $this->belongsTo(TenantDomain::class, 'primary_domain_id');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function mediaAssets(): HasMany
    {
        return $this->hasMany(MediaAsset::class);
    }

    public function theme(): HasOne
    {
        return $this->hasOne(TenantTheme::class)->where('is_active', true);
    }

    public function themes(): HasMany
    {
        return $this->hasMany(TenantTheme::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    public function navigation(): HasOne
    {
        return $this->hasOne(TenantNavigationSettings::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(TenantSocialLink::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(TenantEmailSetting::class);
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        $settingsJson = $this->settings_json ?? [];

        if (isset($settingsJson[$key])) {
            return $settingsJson[$key];
        }

        $setting = $this->settings()->where('key', $key)->first();

        return ($setting && $setting->value !== null) ? $setting->value : $default;
    }
}