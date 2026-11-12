<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lengkapi skema legacy yang dibutuhkan aplikasi.
     *
     * Dump produksi sudah memiliki tabel-tabel ini, tetapi instalasi baru
     * hanya menjalankan migration bawaan Laravel. Migration ini membuat
     * tabel yang hilang dan menyelaraskan kolom users tanpa menghapus data.
     */
    public function up(): void
    {
        $renamedUsersSchema = false;

        if (Schema::hasTable('users')) {
            if (! Schema::hasColumn('users', 'id_user')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->renameColumn('id', 'id_user');
                });
                $renamedUsersSchema = true;
            }

            if (! Schema::hasColumn('users', 'full_name')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->renameColumn('name', 'full_name');
                });
                $renamedUsersSchema = true;
            }

            if (! Schema::hasColumn('users', 'role')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->enum('role', ['sme_owner', 'mentor', 'investor', 'admin', 'user'])
                        ->default('sme_owner');
                });
            }

            if (! Schema::hasColumn('users', 'is_verified')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->boolean('is_verified')->default(false);
                });
            }

            // Kolom ini tidak ada di dump legacy, tetapi dibutuhkan untuk
            // fitur "Ingat saya" pada form login.
            if (! Schema::hasColumn('users', 'remember_token')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('remember_token', 100)->nullable();
                });
            }

            // Hanya skema bawaan Laravel yang baru dibuat (fresh install)
            // yang dipersempit ke panjang kolom legacy varchar(100).
            if ($renamedUsersSchema && DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE users MODIFY full_name varchar(100) NOT NULL, MODIFY email varchar(100) NOT NULL');
            }
        }

        if (! Schema::hasTable('business_categories')) {
            Schema::create('business_categories', function (Blueprint $table) {
                $table->bigIncrements('id_category');
                $table->string('category_name', 100)->unique();
                $table->text('deskripsi')->nullable();
            });
        }

        if (! Schema::hasTable('sme_profiles')) {
            Schema::create('sme_profiles', function (Blueprint $table) {
                $table->bigIncrements('id_profile');
                $table->foreignId('id_user')
                    ->constrained('users', 'id_user')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('id_category')
                    ->constrained('business_categories', 'id_category')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->string('business_name', 100);
                $table->string('legal_type', 100)->nullable();
                $table->text('address')->nullable();
                $table->string('city', 100);
                $table->integer('established_year');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('system_logs')) {
            Schema::create('system_logs', function (Blueprint $table) {
                $table->bigIncrements('id_logs');
                $table->foreignId('id_user')
                    ->constrained('users', 'id_user')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->string('action_type', 100);
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        // Sengaja tidak menghapus tabel/kolom legacy agar rollback tidak
        // menghilangkan data produksi.
    }
};
