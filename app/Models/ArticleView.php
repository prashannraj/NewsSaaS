<?php

namespace App\Models;

use App\Concerns\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleView extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = ['tenant_id', 'organization_id', 'article_id', 'visitor_hash', 'ip_address', 'viewed_at', 'view_date'];

    protected $casts = ['viewed_at' => 'datetime', 'view_date' => 'date'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
