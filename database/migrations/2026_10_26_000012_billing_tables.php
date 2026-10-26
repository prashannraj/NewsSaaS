<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('billing_provider')->default('manual');
            $table->string('provider_price_id')->nullable();
            $table->string('currency', 3);
            $table->unsignedBigInteger('unit_amount');
            $table->string('billing_interval')->nullable();
            $table->unsignedInteger('interval_count')->default(1);
            $table->string('pricing_model')->default('flat');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('billing_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('billing_provider');
            $table->string('provider_customer_id');
            $table->string('email')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['billing_provider', 'provider_customer_id']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('code');
            $table->text('description')->nullable()->after('name');
            $table->unsignedInteger('interval_count')->default(1)->after('billing_interval');
            $table->string('pricing_model')->default('flat')->after('interval_count');
            $table->unsignedBigInteger('base_amount')->nullable()->after('pricing_model');
            $table->unsignedInteger('trial_days')->default(0)->after('currency');
            $table->unsignedInteger('article_limit')->nullable()->after('seat_limit');
            $table->unsignedBigInteger('storage_limit_bytes')->nullable()->after('article_limit');
            $table->unsignedInteger('domain_limit')->nullable()->after('storage_limit_bytes');
            $table->string('status')->default('active')->after('is_active');
            $table->boolean('is_public')->default(true)->after('status');
            $table->unsignedInteger('sort_order')->default(0)->after('is_public');
        });

        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('type')->default('boolean');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('plan_features', function (Blueprint $table) {
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            $table->string('value')->nullable();
            $table->primary(['plan_id', 'feature_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('organization_id')->constrained('products')->nullOnDelete();
            $table->foreignId('product_price_id')->nullable()->after('product_id')->constrained('product_prices')->nullOnDelete();
            $table->string('billing_provider')->default('manual')->after('provider');
            $table->string('provider_customer_id')->nullable()->after('provider_subscription_id');
            $table->char('currency', 3)->default('USD')->after('quantity');
            $table->bigInteger('unit_amount')->nullable()->after('currency');
            $table->timestamp('current_period_starts_at')->nullable()->after('trial_ends_at');
            $table->timestamp('current_period_ends_at')->nullable()->after('current_period_starts_at');
            $table->boolean('cancel_at_period_end')->default(false)->after('current_period_ends_at');
            $table->timestamp('ended_at')->nullable()->after('canceled_at');
        });

        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('product_price_id')->nullable()->constrained('product_prices')->nullOnDelete();
            $table->string('description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->bigInteger('unit_amount');
            $table->timestamps();
        });

        Schema::create('subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->string('metric');
            $table->unsignedBigInteger('quantity');
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['subscription_id', 'metric', 'recorded_at']);
        });

        Schema::rename('invoices', 'billing_invoices');

        Schema::table('billing_invoices', function (Blueprint $table) {
            $table->foreignId('billing_customer_id')->nullable()->after('organization_id')->constrained('billing_customers')->nullOnDelete();
            $table->string('billing_provider')->nullable()->after('subscription_id');
        });

        Schema::create('billing_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_invoice_id')->constrained('billing_invoices')->cascadeOnDelete();
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->bigInteger('unit_amount');
            $table->bigInteger('amount');
            $table->timestamps();
        });

        Schema::create('payment_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->bigInteger('amount');
            $table->string('currency', 3);
            $table->string('provider_refund_id')->nullable()->unique();
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('event_type');
            $table->string('provider_event_id')->unique();
            $table->json('payload');
            $table->string('status')->default('received');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('entitlement_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('key');
            $table->string('value')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'key']);
        });

        Schema::create('organization_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('active');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('organization_seats');
        Schema::dropIfExists('entitlement_overrides');
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('payment_refunds');
        Schema::dropIfExists('billing_invoice_items');
        Schema::dropIfExists('billing_invoices');
        Schema::dropIfExists('billing_customers');
        Schema::dropIfExists('subscription_usage');
        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('features');
        Schema::dropIfExists('product_prices');
        Schema::dropIfExists('products');
        Schema::enableForeignKeyConstraints();
    }
};
