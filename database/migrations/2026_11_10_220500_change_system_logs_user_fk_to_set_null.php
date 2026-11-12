<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_logs', function (Blueprint $table) {
            $table->dropForeign('system_logs_users_FK');
        });

        Schema::table('system_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->nullable()->change();
        });

        Schema::table('system_logs', function (Blueprint $table) {
            $table->foreign('id_user', 'system_logs_users_FK')
                ->references('id_user')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        if (DB::table('system_logs')->whereNull('id_user')->exists()) {
            throw new RuntimeException(
                'Rollback dibatalkan: terdapat audit log tanpa pemilik. Data audit harus dipertahankan.'
            );
        }

        Schema::table('system_logs', function (Blueprint $table) {
            $table->dropForeign('system_logs_users_FK');
        });

        Schema::table('system_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->nullable(false)->change();
        });

        Schema::table('system_logs', function (Blueprint $table) {
            $table->foreign('id_user', 'system_logs_users_FK')
                ->references('id_user')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
