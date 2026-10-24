<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // The final combined score for one student, one subject, one term —
        // this is what actually feeds the Report Card table (CA / Exam / Final / Grade)
        // Calculated by summing each assessment_results row × its weight
        Schema::create('subject_term_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();

            $table->decimal('final_percentage', 5, 2);
            $table->string('grade_letter')->nullable();
            $table->string('achievement_level')->nullable(); // "Proficient", "Developing" — Section 6
            $table->boolean('passed')->default(false);

            // Trend vs previous term — powers the Student Profile page's ↑/↓ arrows
            $table->decimal('previous_term_percentage', 5, 2)->nullable();
            $table->decimal('change_from_previous', 6, 2)->nullable();

            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'term_id']);
            $table->index(['term_id', 'subject_id']);
        });

        // The overall term result — sum/average across all subject_term_results
        // for one student, plus promotion decision
        Schema::create('term_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();

            $table->decimal('overall_percentage', 5, 2);
            $table->string('achievement_level')->nullable();
            $table->unsignedSmallInteger('position')->nullable(); // rank in class
            $table->unsignedSmallInteger('class_size')->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();

            $table->enum('promotion_status', ['promoted', 'conditional', 'not_promoted', 'pending'])->default('pending');
            $table->text('promotion_reason')->nullable(); // human-readable rule trace, e.g. "Mathematics below 40%"

            $table->timestamps();

            $table->unique(['student_id', 'term_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('term_results');
        Schema::dropIfExists('subject_term_results');
    }
};