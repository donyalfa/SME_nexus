<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Langkah action plan pada hasil analisis disimpan per baris, bukan JSON.
     */
    public function up(): void
    {
        Schema::create('intelligence_analysis_finding_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_finding')->constrained('intelligence_analysis_findings')->cascadeOnDelete();
            $table->unsignedSmallInteger('step_order')->default(1);
            $table->text('step');
            $table->timestamps();

            $table->unique(['id_finding', 'step_order']);
        });

        $this->backfillExistingSteps();

        if (Schema::hasColumn('intelligence_analysis_findings', 'action_steps')) {
            Schema::table('intelligence_analysis_findings', function (Blueprint $table) {
                $table->dropColumn('action_steps');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('intelligence_analysis_findings', 'action_steps')) {
            Schema::table('intelligence_analysis_findings', function (Blueprint $table) {
                $table->json('action_steps')->nullable();
            });
        }

        if (Schema::hasTable('intelligence_analysis_finding_steps')) {
            DB::table('intelligence_analysis_finding_steps')
                ->select(['id_finding'])
                ->distinct()
                ->orderBy('id_finding')
                ->get()
                ->each(function ($row): void {
                    $steps = DB::table('intelligence_analysis_finding_steps')
                        ->where('id_finding', $row->id_finding)
                        ->orderBy('step_order')
                        ->orderBy('id')
                        ->pluck('step')
                        ->all();

                    DB::table('intelligence_analysis_findings')
                        ->where('id', $row->id_finding)
                        ->update(['action_steps' => json_encode($steps)]);
                });

            Schema::dropIfExists('intelligence_analysis_finding_steps');
        }
    }

    private function backfillExistingSteps(): void
    {
        if (! Schema::hasColumn('intelligence_analysis_findings', 'action_steps')) {
            return;
        }

        DB::table('intelligence_analysis_findings')
            ->select(['id', 'action_steps'])
            ->orderBy('id')
            ->chunkById(100, function ($findings): void {
                $rows = [];

                foreach ($findings as $finding) {
                    foreach ($this->decode($finding->action_steps) as $index => $step) {
                        if (! is_string($step) || trim($step) === '') {
                            continue;
                        }

                        $rows[] = [
                            'id_finding' => $finding->id,
                            'step_order' => $index + 1,
                            'step' => trim($step),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if ($rows !== []) {
                    DB::table('intelligence_analysis_finding_steps')->insertOrIgnore($rows);
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
