<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // One row per student per assessment — the SUM of their student_marks rows,
        // pre-calculated so dashboards/analytics don't have to re-sum every time
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_obtained', 6, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('grade_letter')->nullable(); // resolved against grading_scales at calc time
            $table->boolean('passed')->default(false);

            // Audit trail — Section 28's "any correction creates an Audit Log"
            $table->foreignId('last_modified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'assessment_id']);
            $table->index(['assessment_id', 'percentage']);
        });

        // Simple audit trail for mark corrections after locking
        Schema::create('result_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->string('field_changed'); // e.g. "Question 14" or "total_obtained"
            $table->string('old_value');
            $table->string('new_value');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_audit_logs');
        Schema::dropIfExists('assessment_results');
    }
};