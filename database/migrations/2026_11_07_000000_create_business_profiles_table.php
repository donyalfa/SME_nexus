<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel profil usaha hasil onboarding setelah registrasi.
     * Terpisah dari sme_profiles yang dipakai panel admin Intelligence.
     */
    public function up(): void
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            // PK mengikuti konvensi tabel legacy: id_namatabel.
            $table->id('id_business_profile');

            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();

            // Kategori tidak ikut terhapus saat kategori dihapus di admin.
            $table->foreignId('id_category')
                ->constrained('business_categories', 'id_category');

            $table->string('business_name', 100);
            $table->string('legal_type', 100)->nullable();   // Perorangan, CV, PT, ...
            $table->string('city', 100);
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();         // No. telepon / WhatsApp
            $table->string('business_email', 100)->nullable();
            $table->string('website', 255)->nullable();      // Website / media sosial
            $table->string('nib', 50)->nullable();           // Nomor Induk Berusaha
            $table->string('npwp', 50)->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->unsignedInteger('established_year');
            $table->text('description')->nullable();         // Deskripsi usaha

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
