php artisan tinker --execute="$(cat /tmp/test-user-cascade.php)"<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simpan metadata yang memengaruhi hasil evaluasi pada level versi.
     *
     * Sebelumnya diagnosis, severity, dan priority hanya tersimpan pada
     * autopsy_rules sehingga perubahan draft langsung mengubah hasil rule
     * yang sedang aktif. Snapshot pada versi membuat rule terpublikasi tetap
     * stabil sampai versi baru benar-benar dipublikasikan.
     */
    public function up(): void
    {
        if (! Schema::hasTable('autopsy_rule_versions')) {
            return;
        }

        Schema::table('autopsy_rule_versions', function (Blueprint $table) {
            if (! Schema::hasColumn('autopsy_rule_versions', 'id_diagnosis')) {
                $table->foreignId('id_diagnosis')->nullable()->index();
            }

            if (! Schema::hasColumn('autopsy_rule_versions', 'severity')) {
                $table->string('severity', 20)->nullable();
            }

            if (! Schema::hasColumn('autopsy_rule_versions', 'priority')) {
                $table->unsignedSmallInteger('priority')->nullable();
            }
        });

        // Bekukan nilai yang saat ini dipakai oleh setiap versi. Tanpa
        // backfill, versi lama yang kolom barunya null akan jatuh kembali ke
        // nilai rule yang mungkin sudah berubah.
        if (Schema::hasTable('autopsy_rules')) {
            DB::table('autopsy_rule_versions')
                ->select(['id', 'id_rule'])
                ->orderBy('id')
                ->chunkById(200, function ($versions) {
                    foreach ($versions as $version) {
                        $rule = DB::table('autopsy_rules')
                            ->select(['id_diagnosis', 'severity', 'priority'])
                            ->where('id', $version->id_rule)
                            ->first();

                        if ($rule === null) {
                            continue;
                        }

                        DB::table('autopsy_rule_versions')
                            ->where('id', $version->id)
                            ->update([
                                'id_diagnosis' => $rule->id_diagnosis,
                                'severity' => $rule->severity,
                                'priority' => $rule->priority,
                            ]);
                    }
                });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('autopsy_rule_versions')) {
            return;
        }

        Schema::table('autopsy_rule_versions', function (Blueprint $table) {
            if (Schema::hasColumn('autopsy_rule_versions', 'priority')) {
                $table->dropColumn('priority');
            }

            if (Schema::hasColumn('autopsy_rule_versions', 'severity')) {
                $table->dropColumn('severity');
            }

            if (Schema::hasColumn('autopsy_rule_versions', 'id_diagnosis')) {
                $table->dropColumn('id_diagnosis');
            }
        });
    }
};
