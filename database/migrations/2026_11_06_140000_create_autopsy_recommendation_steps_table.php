<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Langkah action plan rekomendasi master dipisah menjadi baris tersendiri.
     */
    public function up(): void
    {
        Schema::create('autopsy_recommendation_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_recommendation')->constrained('autopsy_recommendations')->cascadeOnDelete();
            $table->unsignedSmallInteger('step_order')->default(1);
            $table->text('step');
            $table->timestamps();

            $table->unique(['id_recommendation', 'step_order']);
        });

        $this->backfillExistingSteps();

        if (Schema::hasColumn('autopsy_recommendations', 'action_steps')) {
            Schema::table('autopsy_recommendations', function (Blueprint $table) {
                $table->dropColumn('action_steps');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('autopsy_recommendations', 'action_steps')) {
            Schema::table('autopsy_recommendations', function (Blueprint $table) {
                $table->json('action_steps')->nullable();
            });
        }

        if (Schema::hasTable('autopsy_recommendation_steps')) {
            DB::table('autopsy_recommendation_steps')
                ->select(['id_recommendation'])
                ->distinct()
                ->orderBy('id_recommendation')
                ->get()
                ->each(function ($row): void {
                    $steps = DB::table('autopsy_recommendation_steps')
                        ->where('id_recommendation', $row->id_recommendation)
                        ->orderBy('step_order')
                        ->orderBy('id')
                        ->pluck('step')
                        ->all();

                    DB::table('autopsy_recommendations')
                        ->where('id', $row->id_recommendation)
                        ->update(['action_steps' => json_encode($steps)]);
                });

            Schema::dropIfExists('autopsy_recommendation_steps');
        }
    }

    private function backfillExistingSteps(): void
    {
        if (! Schema::hasColumn('autopsy_recommendations', 'action_steps')) {
            return;
        }

        DB::table('autopsy_recommendations')
            ->select(['id', 'action_steps'])
            ->orderBy('id')
            ->chunkById(100, function ($recommendations): void {
                $rows = [];

                foreach ($recommendations as $recommendation) {
                    foreach ($this->decode($recommendation->action_steps) as $index => $step) {
                        if (! is_string($step) || trim($step) === '') {
                            continue;
                        }

                        $rows[] = [
                            'id_recommendation' => $recommendation->id,
                            'step_order' => $index + 1,
                            'step' => trim($step),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if ($rows !== []) {
                    DB::table('autopsy_recommendation_steps')->insertOrIgnore($rows);
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
