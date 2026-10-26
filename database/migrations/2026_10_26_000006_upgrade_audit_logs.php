<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->renameColumn('user_id', 'actor_user_id');
            $table->renameColumn('action', 'event');
            $table->renameColumn('metadata', 'metadata_json');
            $table->string('request_id')->nullable()->after('user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn('request_id');
            $table->renameColumn('metadata_json', 'metadata');
            $table->renameColumn('event', 'action');
            $table->renameColumn('actor_user_id', 'user_id');
        });
    }
};
