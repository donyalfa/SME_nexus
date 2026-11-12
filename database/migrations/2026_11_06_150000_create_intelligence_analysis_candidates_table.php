<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kandidat rule (hasil ranking) disimpan sebagai baris agar bisa dianalisis.
     */
    public function up(): void
    {
        Schema::create('intelligence_analysis_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_analysis')->constrained('intelligence_analyses')->cascadeOnDelete();
            $table->foreignId('id_rule')->nullable()->constrained('autopsy_rules')->nullOnDelete();
            $table->foreignId('id_rule_version')->nullable()->constrained('autopsy_rule_versions')->nullOnDelete();
            $table->foreignId('id_diagnosis')->nullable()->constrained('autopsy_diagnoses')->nullOnDelete();
            $table->string('code', 100);
            $table->string('severity', 20)->nullable();
            $table->unsignedSmallInteger('priority')->nullable();
            $table->unsignedSmallInteger('specificity')->nullable();
            $table->unsignedSmallInteger('rank_position')->default(1);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['id_analysis', 'id_rule']);
            $table->index(['id_analysis', 'rank_position']);
        });

        $this->backfillExistingCandidates();

        if (Schema::hasColumn('intelligence_analyses', 'matched_candidates')) {
            Schema::table('intelligence_analyses', function (Blueprint $table) {
                $table->dropColumn('matched_candidates');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('intelligence_analyses', 'matched_candidates')) {
            Schema::table('intelligence_analyses', function (Blueprint $table) {
                $table->json('matched_candidates')->nullable()->after('primary_rule_version_id');
            });
        }

        if (Schema::hasTable('intelligence_analysis_candidates')) {
            DB::table('intelligence_analysis_candidates')
                ->select(['id_analysis'])
                ->distinct()
                ->orderBy('id_analysis')
                ->get()
                ->each(function ($row): void {
                    $candidates = DB::table('intelligence_analysis_candidates')
                        ->where('id_analysis', $row->id_analysis)
                        ->orderBy('rank_position')
                        ->orderBy('id')
                        ->get()
                        ->map(fn ($candidate): array => [
                            'rule_id' => $candidate->id_rule,
                            'code' => $candidate->code,
                            'severity' => $candidate->severity,
                            'specificity' => $candidate->specificity,
                        ])
                        ->all();

                    DB::table('intelligence_analyses')
                        ->where('id', $row->id_analysis)
                        ->update(['matched_candidates' => json_encode($candidates)]);
                });

            Schema::dropIfExists('intelligence_analysis_candidates');
        }
    }

    private function backfillExistingCandidates(): void
    {
        if (! Schema::hasColumn('intelligence_analyses', 'matched_candidates')) {
            return;
        }

        DB::table('intelligence_analyses')
            ->select(['id', 'matched_candidates'])
            ->orderBy('id')
            ->chunkById(100, function ($analyses): void {
                $decoded = $analyses->mapWithKeys(fn ($analysis): array => [
                    $analysis->id => $this->decode($analysis->matched_candidates),
                ]);

                $ruleIds = $decoded->flatten(1)
                    ->pluck('rule_id')
                    ->filter(fn ($id): bool => is_numeric($id))
                    ->map(fn ($id): int => (int) $id)
                    ->unique()
                    ->values()
                    ->all();

                $rules = $ruleIds === []
                    ? collect()
                    : DB::table('autopsy_rules')->whereIn('id', $ruleIds)->get()->keyBy('id');

                $versions = $ruleIds === []
                    ? collect()
                    : DB::table('autopsy_rule_versions')
                        ->whereIn('id_rule', $ruleIds)
                        ->where('status', 'published')
                        ->where('is_active', true)
                        ->orderByDesc('version')
                        ->get()
                        ->groupBy('id_rule');

                $rows = [];

                foreach ($decoded as $analysisId => $candidates) {
                    foreach ($candidates as $index => $candidate) {
                        if (! is_array($candidate) || ! isset($candidate['code'])) {
                            continue;
                        }

                        $ruleId = is_numeric($candidate['rule_id'] ?? null) ? (int) $candidate['rule_id'] : null;
                        $rule = $ruleId !== null ? $rules->get($ruleId) : null;
                        $version = $ruleId !== null ? $versions->get($ruleId)?->first() : null;

                        $rows[] = [
                            'id_analysis' => $analysisId,
                            'id_rule' => $ruleId,
                            'id_rule_version' => $version->id ?? null,
                            'id_diagnosis' => $rule->id_diagnosis ?? null,
                            'code' => mb_substr((string) $candidate['code'], 0, 100),
                            'severity' => is_string($candidate['severity'] ?? null) ? $candidate['severity'] : null,
                            'priority' => $rule->priority ?? null,
                            'specificity' => is_numeric($candidate['specificity'] ?? null) ? (int) $candidate['specificity'] : null,
                            'rank_position' => $index + 1,
                            'is_primary' => $index === 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if ($rows !== []) {
                    DB::table('intelligence_analysis_candidates')->insertOrIgnore($rows);
                }
            });
    }

    /**
     * @return array<int, mixed>
     */
    private function decode(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if (! is_string($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? array_values($decoded) : [];
    }
};
