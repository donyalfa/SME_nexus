<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel hasil analisis Business Autopsy pada modul Intelligence.
     *
     * Menyimpan snapshot data mentah yang diinput pemilik UMKM beserta
     * hasil diagnosa, rekomendasi, dan status analisis. Record ini menjadi
     * sumber data bagi modul Decision berikutnya.
     */
    public function up(): void
    {
        Schema::create('intelligence_analyses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();

            // Konteks periode (opsional) untuk kronologi modul Decision.
            $table->date('period_prev_date')->nullable();
            $table->date('period_current_date')->nullable();

            // Data mentah yang diinput pengguna.
            $table->decimal('omzet_prev', 15, 2);
            $table->decimal('omzet_current', 15, 2);
            $table->decimal('customer_prev', 15, 2);
            $table->decimal('customer_current', 15, 2);
            $table->decimal('sales_prev', 15, 2);
            $table->decimal('sales_current', 15, 2);
            $table->decimal('product_count', 10, 2)->default(0);
            $table->decimal('avg_price', 15, 2);
            $table->decimal('marketing_spend', 15, 2);
            $table->decimal('operational_cost', 15, 2);

            // Hasil analisis.
            $table->decimal('omzet_change', 8, 2);
            $table->decimal('customer_change', 8, 2);
            $table->decimal('sales_change', 8, 2);
            $table->string('status');
            $table->json('diagnoses');
            $table->json('recommendations');

            $table->timestamps();

            $table->index(['id_user', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intelligence_analyses');
    }
};
