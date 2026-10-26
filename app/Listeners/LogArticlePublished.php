<?php

namespace App\Listeners;

use App\Events\ArticlePublished;
use App\Models\AuditLog;

class LogArticlePublished
{
    public function handle(ArticlePublished $event): void
    {
        AuditLog::create([
            'organization_id' => $event->article->organization_id,
            'action' => 'article.published',
            'subject_type' => 'article',
            'subject_id' => $event->article->id,
            'created_at' => now(),
        ]);
    }
}
