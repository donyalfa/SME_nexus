<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_action_plans', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();

            $table->foreignId('id_analysis')
                ->constrained('intelligence_analyses')
                ->cascadeOnDelete();

            $table->foreignId('id_finding')
                ->nullable()
                ->constrained('intelligence_analysis_findings')
                ->nullOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users', 'id_user')
                ->nullOnDelete();

            $table->string('task', 200);
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('todo');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['id_user', 'status', 'due_date'], 'dap_owner_status_due_idx');
            $table->index(['id_analysis', 'id_finding'], 'dap_analysis_finding_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_action_plans');
    }
};
