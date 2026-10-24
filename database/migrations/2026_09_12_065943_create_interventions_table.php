<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('area'); // "Algebra", "Reading Comprehension"
            $table->enum('trigger', [
                'declining_performance',
                'low_completion',
                'repeated_failures',
                'low_attendance',
                'performance_gap',
                'manual',
            ])->default('manual');
            $table->text('problem_description');

            $table->enum('intervention_type', [
                'remedial_lessons',
                'one_on_one',
                'peer_support',
                'extra_practice',
                'counseling_referral',
                'parent_meeting',
                'other',
            ]);
            $table->enum('priority', ['low', 'moderate', 'high'])->default('moderate');

            $table->date('start_date');
            $table->date('review_date');

            $table->decimal('score_before', 5, 2);
            $table->decimal('score_target', 5, 2);
            $table->decimal('score_after', 5, 2)->nullable(); // filled in at review

            $table->enum('status', ['active', 'completed', 'overdue', 'cancelled'])->default('active');
            $table->timestamps();

            $table->index(['school_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};