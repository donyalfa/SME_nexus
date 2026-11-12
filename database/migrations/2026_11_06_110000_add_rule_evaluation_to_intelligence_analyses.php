<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intelligence_analyses', function (Blueprint $table) {
            if (! Schema::hasColumn('intelligence_analyses', 'primary_diagnosis_id')) {
                $table->foreignId('primary_diagnosis_id')
                    ->nullable()
                    ->after('recommendations')
                    ->constrained('autopsy_diagnoses')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('intelligence_analyses', 'primary_rule_version_id')) {
                $table->foreignId('primary_rule_version_id')
                    ->nullable()
                    ->after('primary_diagnosis_id')
                    ->constrained('autopsy_rule_versions')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('intelligence_analyses', 'detected_symptoms')) {
                $table->json('detected_symptoms')->nullable()->after('primary_rule_version_id');
            }

            if (! Schema::hasColumn('intelligence_analyses', 'matched_candidates')) {
                $table->json('matched_candidates')->nullable()->after('detected_symptoms');
            }
        });
    }

    public function down(): void
    {
        Schema::table('intelligence_analyses', function (Blueprint $table) {
            if (Schema::hasColumn('intelligence_analyses', 'matched_candidates')) {
                $table->dropColumn('matched_candidates');
            }
            if (Schema::hasColumn('intelligence_analyses', 'detected_symptoms')) {
                $table->dropColumn('detected_symptoms');
            }
            if (Schema::hasColumn('intelligence_analyses', 'primary_rule_version_id')) {
                $table->dropConstrainedForeignId('primary_rule_version_id');
            }
            if (Schema::hasColumn('intelligence_analyses', 'primary_diagnosis_id')) {
                $table->dropConstrainedForeignId('primary_diagnosis_id');
            }
        });
    }
};
