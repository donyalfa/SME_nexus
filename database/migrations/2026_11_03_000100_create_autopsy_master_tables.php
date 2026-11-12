<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel master data dan audit untuk modul Business Autopsy.
     */
    public function up(): void
    {
        Schema::create('autopsy_symptoms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('metric_key', 100);
            $table->enum('operator', [
                'greater_than',
                'greater_than_or_equal',
                'less_than',
                'less_than_or_equal',
                'equal',
                'not_equal',
            ]);
            $table->decimal('threshold', 15, 4);
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'metric_key']);
        });

        Schema::create('autopsy_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'severity', 'priority']);
        });

        Schema::create('autopsy_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->foreignId('id_diagnosis')->constrained('autopsy_diagnoses')->restrictOnDelete();
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'id_diagnosis']);
        });

        Schema::create('autopsy_rule_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rule')->constrained('autopsy_rules')->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_active')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->timestamps();

            $table->unique(['id_rule', 'version']);
            $table->index(['id_rule', 'is_active']);
        });

        Schema::create('autopsy_rule_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rule_version')->constrained('autopsy_rule_versions')->cascadeOnDelete();
            $table->unsignedSmallInteger('group_order')->default(1);
            $table->string('label', 100)->nullable();
            $table->timestamps();

            $table->unique(['id_rule_version', 'group_order']);
        });

        Schema::create('autopsy_rule_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rule_group')->constrained('autopsy_rule_groups')->cascadeOnDelete();
            $table->foreignId('id_symptom')->constrained('autopsy_symptoms')->restrictOnDelete();
            $table->boolean('is_negated')->default(false);
            $table->timestamps();

            $table->unique(['id_rule_group', 'id_symptom']);
        });

        Schema::create('autopsy_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_diagnosis')->constrained('autopsy_diagnoses')->cascadeOnDelete();
            $table->string('code', 100)->unique();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->json('action_steps')->nullable();
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->timestamps();

            $table->index(['id_diagnosis', 'is_active']);
        });

        Schema::create('autopsy_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->string('action', 100);
            $table->string('entity_type', 100);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autopsy_audit_logs');
        Schema::dropIfExists('autopsy_recommendations');
        Schema::dropIfExists('autopsy_rule_conditions');
        Schema::dropIfExists('autopsy_rule_groups');
        Schema::dropIfExists('autopsy_rule_versions');
        Schema::dropIfExists('autopsy_rules');
        Schema::dropIfExists('autopsy_diagnoses');
        Schema::dropIfExists('autopsy_symptoms');
    }
};
