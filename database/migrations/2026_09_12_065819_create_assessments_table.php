<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('name'); // "Mid-Term Examination"
            $table->enum('category', ['formative', 'summative', 'diagnostic'])->default('summative');
            $table->unsignedSmallInteger('total_marks');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->unsignedTinyInteger('pass_mark')->nullable(); // percentage
            $table->decimal('contribution_percentage', 5, 2)->nullable(); // overrides assessment_weights if set

            // Governance workflow — Section 28/29
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'moderation',
                'approved',
                'published',
                'locked',
                'amended',
            ])->default('draft');

            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'term_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};