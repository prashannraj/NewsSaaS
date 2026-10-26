<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('organization_users', 'organization_memberships');

        try {
            Schema::table('organization_memberships', function (Blueprint $table) {
                $table->dropIndex('organization_users_role_index');
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('organization_memberships', function (Blueprint $table) {
                $table->dropIndex('organization_memberships_role_index');
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table('organization_memberships', function (Blueprint $table) {
                $table->dropIndex(['role']);
            });
        } catch (Throwable $e) {
        }

        Schema::table('organization_memberships', function (Blueprint $table) {
            $table->string('membership_status')->default('active')->after('user_id');
            $table->dropColumn('role');
            $table->index('membership_status');
        });
    }

    public function down(): void
    {
        Schema::table('organization_memberships', function (Blueprint $table) {
            $table->dropIndex(['membership_status']);
            $table->string('role')->default('viewer');
            $table->dropColumn('membership_status');
        });

        Schema::rename('organization_memberships', 'organization_users');
    }
};
