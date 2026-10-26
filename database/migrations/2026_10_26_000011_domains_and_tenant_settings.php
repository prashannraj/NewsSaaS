<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('domains', 'tenant_domains');

        Schema::table('tenant_domains', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            $table->renameColumn('domain', 'normalized_domain');
            $table->string('domain')->nullable()->after('normalized_domain');
            $table->string('domain_type')->default('subdomain')->after('domain');
            $table->string('verification_method')->nullable()->after('type');
            $table->string('verification_token_hash')->nullable()->after('verification_token');
            $table->string('dns_status')->nullable()->after('verified_at');
            $table->string('ssl_status')->nullable()->change();
            $table->timestamp('ssl_expires_at')->nullable()->after('ssl_status');
            $table->timestamp('last_checked_at')->nullable()->after('ssl_expires_at');
            $table->text('failure_reason')->nullable()->after('last_checked_at');
        });

        Schema::table('tenant_domains', function (Blueprint $table) {
            $table->unique('normalized_domain');
        });

        Schema::create('domain_verification_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_domain_id')->constrained('tenant_domains')->cascadeOnDelete();
            $table->string('token_hash');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tenant_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->json('settings');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('tenant_navigation_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->json('settings');
            $table->timestamps();

            $table->unique('tenant_id');
        });

        Schema::create('tenant_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('platform');
            $table->string('url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tenant_email_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('mailer')->default('smtp');
            $table->string('from_address')->nullable();
            $table->string('from_name')->nullable();
            $table->string('secret_reference')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('tenant_email_settings');
        Schema::dropIfExists('tenant_social_links');
        Schema::dropIfExists('tenant_navigation_settings');
        Schema::dropIfExists('tenant_themes');
        Schema::dropIfExists('domain_verification_tokens');

        Schema::table('tenant_domains', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropUnique(['normalized_domain']);
            $table->dropColumn([
                'tenant_id', 'domain', 'domain_type', 'verification_method',
                'verification_token_hash', 'dns_status', 'ssl_expires_at',
                'last_checked_at', 'failure_reason',
            ]);
            $table->renameColumn('normalized_domain', 'domain');
        });

        Schema::rename('tenant_domains', 'domains');
        Schema::enableForeignKeyConstraints();
    }
};
