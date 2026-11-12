<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_data', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_profile')
                ->constrained('sme_profiles', 'id_profile')
                ->cascadeOnDelete();

            $table->date('period_date');
            $table->decimal('omzet', 15, 2)->default(0);
            $table->decimal('pendapatan', 15, 2)->default(0);
            $table->decimal('pengeluaran', 15, 2)->default(0);
            $table->decimal('laba', 15, 2)->default(0);
            $table->integer('jumlah_transaksi')->default(0);
            $table->integer('jumlah_pelanggan')->default(0);
            $table->integer('produk_terjual')->default(0);
            $table->integer('produk_tidak_terjual')->default(0);
            $table->integer('stok')->default(0);
            $table->decimal('biaya_operasional', 15, 2)->default(0);
            $table->decimal('biaya_pemasaran', 15, 2)->default(0);
            $table->text('data_pendukung')->nullable();

            $table->timestamps();

            $table->unique(['id_profile', 'period_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_data');
    }
};
