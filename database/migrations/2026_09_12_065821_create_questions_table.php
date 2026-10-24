<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('question_number');
            $table->text('question_text');

            // The "question intelligence" metadata from Section 9
            $table->string('topic')->nullable();
            $table->string('learning_objective')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->enum('cognitive_level', [
                'remember',
                'understand',
                'apply',
                'analyse',
                'evaluate',
                'create',
            ])->default('apply');
            $table->enum('question_type', ['short_answer', 'essay', 'mcq', 'calculation'])->default('short_answer');
            $table->unsignedTinyInteger('max_marks');

            $table->timestamps();

            $table->unique(['assessment_id', 'question_number']);
            $table->index(['topic']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};