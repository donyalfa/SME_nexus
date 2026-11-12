<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gejala terdeteksi dipindahkan dari kolom JSON menjadi baris tersendiri
     * agar bisa dihitung dan di-join dengan master gejala.
     */
    public function up(): void
    {
        Schema::create('intelligence_analysis_symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_analysis')->constrained('intelligence_analyses')->cascadeOnDelete();
            $table->foreignId('id_symptom')->nullable()->constrained('autopsy_symptoms')->nullOnDelete();
            $table->string('code', 100);
            $table->string('name', 150);
            $table->string('metric_key', 100);
            $table->timestamps();

            $table->unique(['id_analysis', 'id_symptom']);
        });

        $this->backfillExistingSymptoms();

        if (Schema::hasColumn('intelligence_analyses', 'detected_symptoms')) {
            Schema::table('intelligence_analyses', function (Blueprint $table) {
                $table->dropColumn('detected_symptoms');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('intelligence_analyses', 'detected_symptoms')) {
            Schema::table('intelligence_analyses', function (Blueprint $table) {
                $table->json('detected_symptoms')->nullable()->after('primary_rule_version_id');
            });
        }

        if (Schema::hasTable('intelligence_analysis_symptoms')) {
            DB::table('intelligence_analysis_symptoms')
                ->select(['id_analysis'])
                ->distinct()
                ->orderBy('id_analysis')
                ->get()
                ->each(function ($row): void {
                    $symptoms = DB::table('intelligence_analysis_symptoms')
                        ->where('id_analysis', $row->id_analysis)
                        ->orderBy('id')
                        ->get()
                        ->map(fn ($symptom): array => [
                            'id' => $symptom->id_symptom,
                            'code' => $symptom->code,
                            'name' => $symptom->name,
                            'metric_key' => $symptom->metric_key,
                        ])
                        ->all();

                    DB::table('intelligence_analyses')
                        ->where('id', $row->id_analysis)
                        ->update(['detected_symptoms' => json_encode($symptoms)]);
                });

            Schema::dropIfExists('intelligence_analysis_symptoms');
        }
    }

    private function backfillExistingSymptoms(): void
    {
        if (! Schema::hasColumn('intelligence_analyses', 'detected_symptoms')) {
            return;
        }

        DB::table('intelligence_analyses')
            ->select(['id', 'detected_symptoms'])
            ->orderBy('id')
            ->chunkById(100, function ($analyses): void {
                $rows = [];

                foreach ($analyses as $analysis) {
                    foreach ($this->decode($analysis->detected_symptoms) as $item) {
                        if (! is_array($item) || ! isset($item['code'])) {
                            continue;
                        }

                        $rows[] = [
                            'id_analysis' => $analysis->id,
                            'id_symptom' => is_numeric($item['id'] ?? null) ? (int) $item['id'] : null,
                            'code' => mb_substr((string) $item['code'], 0, 100),
                            'name' => mb_substr((string) ($item['name'] ?? $item['code']), 0, 150),
                            'metric_key' => mb_substr((string) ($item['metric_key'] ?? ''), 0, 100),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if ($rows !== []) {
                    DB::table('intelligence_analysis_symptoms')->insertOrIgnore($rows);
                }
            });
    }

    /**
     * @return array<int, mixed>
     */
    private function decode(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }
};
