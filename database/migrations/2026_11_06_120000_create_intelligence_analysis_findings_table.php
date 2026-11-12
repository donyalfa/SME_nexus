<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hasil analisis (diagnosis & rekomendasi) dipindahkan dari kolom JSON
     * menjadi baris tersendiri supaya bisa di-query, di-join, dan dihitung.
     */
    public function up(): void
    {
        Schema::create('intelligence_analysis_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_analysis')->constrained('intelligence_analyses')->cascadeOnDelete();
            $table->string('type', 20); // diagnosis | recommendation
            $table->string('source', 20)->default('fallback'); // rule | fallback
            $table->unsignedBigInteger('ref_id')->nullable(); // id master diagnosis / recommendation
            $table->foreignId('id_rule_version')->nullable()->constrained('autopsy_rule_versions')->nullOnDelete();
            $table->string('code', 100)->nullable();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('severity', 20)->nullable();
            $table->unsignedSmallInteger('priority')->nullable();
            $table->string('priority_label', 20)->nullable();
            $table->json('action_steps')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['id_analysis', 'type', 'sort_order'], 'iaf_analysis_type_order_index');
        });

        $this->backfillExistingResults();

        if (Schema::hasColumn('intelligence_analyses', 'diagnoses')) {
            Schema::table('intelligence_analyses', function (Blueprint $table) {
                $table->dropColumn(['diagnoses', 'recommendations']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('intelligence_analyses', function (Blueprint $table) {
            if (! Schema::hasColumn('intelligence_analyses', 'diagnoses')) {
                $table->json('diagnoses')->nullable()->after('status');
            }

            if (! Schema::hasColumn('intelligence_analyses', 'recommendations')) {
                $table->json('recommendations')->nullable()->after('diagnoses');
            }
        });

        Schema::dropIfExists('intelligence_analysis_findings');
    }

    /**
     * Pindahkan isi kolom JSON lama ke tabel findings agar tidak ada data hilang.
     */
    private function backfillExistingResults(): void
    {
        if (! Schema::hasColumn('intelligence_analyses', 'diagnoses')) {
            return;
        }

        DB::table('intelligence_analyses')
            ->select(['id', 'diagnoses', 'recommendations'])
            ->orderBy('id')
            ->chunkById(100, function ($analyses): void {
                $rows = [];

                foreach ($analyses as $analysis) {
                    $items = [
                        'diagnosis' => $this->decode($analysis->diagnoses),
                        'recommendation' => $this->decode($analysis->recommendations),
                    ];

                    foreach ($items as $type => $list) {
                        foreach ($list as $index => $item) {
                            if (! is_array($item)) {
                                continue;
                            }

                            $priority = $item['priority'] ?? null;

                            $rows[] = [
                                'id_analysis' => $analysis->id,
                                'type' => $type,
                                'source' => 'fallback',
                                'ref_id' => null,
                                'id_rule_version' => null,
                                'code' => is_string($item['code'] ?? null) ? $item['code'] : null,
                                'title' => mb_substr((string) ($item['title'] ?? 'Tanpa judul'), 0, 150),
                                'description' => $item['description'] ?? null,
                                'severity' => is_string($item['severity'] ?? null) ? $item['severity'] : null,
                                'priority' => is_numeric($priority) ? (int) $priority : null,
                                'priority_label' => is_numeric($priority)
                                    ? $this->priorityLabel((int) $priority)
                                    : (is_string($priority) ? $priority : null),
                                'action_steps' => isset($item['action_steps']) ? json_encode($item['action_steps']) : null,
                                'sort_order' => $index,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }

                if ($rows !== []) {
                    DB::table('intelligence_analysis_findings')->insert($rows);
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

    private function priorityLabel(int $priority): string
    {
        return match (true) {
            $priority <= 1 => 'high',
            $priority === 2 => 'medium',
            default => 'low',
        };
    }
};
