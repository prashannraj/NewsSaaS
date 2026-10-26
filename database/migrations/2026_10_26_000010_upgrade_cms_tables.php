<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unique(['tenant_id', 'slug']);
            $table->foreignId('parent_id')->nullable()->after('tenant_id')->constrained('categories')->nullOnDelete();
            $table->string('image_path')->nullable()->after('description');
            $table->unsignedInteger('sort_order')->default(0)->after('image_path');
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->softDeletes();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->unique(['tenant_id', 'name']);
            $table->unique(['tenant_id', 'slug']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->unique(['tenant_id', 'slug']);
            $table->foreignId('editor_id')->nullable()->after('author_id')->constrained('users')->nullOnDelete();
            $table->string('visibility')->default('public')->after('status');
            $table->string('language_code', 8)->default('en')->after('visibility');
            $table->timestamp('scheduled_at')->nullable()->after('published_at');
            $table->string('canonical_url')->nullable();
            $table->boolean('allow_indexing')->default(true);
            $table->softDeletes();
        });

        Schema::table('media_assets', function (Blueprint $table) {
            $table->unsignedInteger('width')->nullable()->after('size_bytes');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->text('caption')->nullable()->after('alt_text');
            $table->string('checksum', 64)->nullable()->after('caption');
        });

        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('advertiser_name')->nullable()->after('tenant_id');
            $table->string('image_path')->nullable()->after('title');
            $table->unsignedBigInteger('impressions_count')->default(0);
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->string('status')->default('draft')->after('ends_at');
        });

        Schema::create('article_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('edited_by')->constrained('users')->cascadeOnDelete();
            $table->string('title_snapshot');
            $table->longText('body_snapshot');
            $table->string('change_summary')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'article_id']);
        });

        Schema::create('article_media', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('media_asset_id')->constrained('media_assets')->cascadeOnDelete();
            $table->string('collection_name')->default('default');
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['article_id', 'media_asset_id', 'collection_name']);
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('location');
            $table->timestamps();

            $table->unique(['tenant_id', 'location']);
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('target')->default('_self');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['menu_id', 'parent_id', 'sort_order']);
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('content');
            $table->string('status')->default('draft');
            $table->string('template')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('article_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->string('visitor_hash', 64);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('viewed_at');
            $table->date('view_date');

            $table->unique(['article_id', 'visitor_hash', 'view_date']);
            $table->index(['tenant_id', 'viewed_at']);
        });

        Schema::create('editorial_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();
            $table->string('author_name')->nullable();
            $table->string('author_email')->nullable();
            $table->text('body');
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('comments');
        Schema::dropIfExists('editorial_workflows');
        Schema::dropIfExists('article_views');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('article_media');
        Schema::dropIfExists('article_revisions');

        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn(['advertiser_name', 'image_path', 'impressions_count', 'clicks_count', 'status']);
        });
        Schema::table('media_assets', function (Blueprint $table) {
            $table->dropColumn(['width', 'height', 'caption', 'checksum']);
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['editor_id']);
            $table->dropUnique(['tenant_id', 'slug']);
            $table->dropSoftDeletes();
            $table->dropColumn(['editor_id', 'visibility', 'language_code', 'scheduled_at', 'canonical_url', 'allow_indexing']);
        });
        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'name']);
            $table->dropUnique(['tenant_id', 'slug']);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropUnique(['tenant_id', 'slug']);
            $table->dropSoftDeletes();
            $table->dropColumn(['parent_id', 'image_path', 'sort_order', 'is_active']);
        });
        Schema::enableForeignKeyConstraints();
    }
};
