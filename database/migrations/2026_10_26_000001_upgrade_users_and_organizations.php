<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('public_id', 26)->nullable()->unique()->after('id');
            $table->string('avatar_path')->nullable()->after('email');
            $table->string('status')->default('active')->after('email_verified_at');
            $table->string('locale', 8)->default('en')->after('status');
            $table->string('timezone')->default('UTC')->after('locale');
            $table->timestamp('last_login_at')->nullable()->after('timezone');
            $table->softDeletes();
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->renameColumn('owner_id', 'owner_user_id');
            $table->string('public_id', 26)->nullable()->unique()->after('id');
            $table->string('legal_name')->nullable()->after('name');
            $table->string('organization_type')->default('media')->after('legal_name');
            $table->string('contact_email')->nullable()->after('organization_type');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->string('address_line_1')->nullable()->after('contact_phone');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('city')->nullable()->after('address_line_2');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->char('country_code', 2)->nullable()->after('postal_code');
            $table->string('tax_number')->nullable()->after('country_code');
            $table->timestamp('onboarding_completed_at')->nullable()->after('status');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'public_id', 'legal_name', 'organization_type', 'contact_email', 'contact_phone',
                'address_line_1', 'address_line_2', 'city', 'state', 'postal_code',
                'country_code', 'tax_number', 'onboarding_completed_at',
            ]);
            $table->renameColumn('owner_user_id', 'owner_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'public_id', 'avatar_path', 'status', 'locale', 'timezone', 'last_login_at',
            ]);
        });
        Schema::enableForeignKeyConstraints();
    }
};
