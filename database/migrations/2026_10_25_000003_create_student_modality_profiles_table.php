<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_modality_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->decimal('audio_score', 5, 2)->default(0);
            $table->decimal('visual_score', 5, 2)->default(0);
            $table->decimal('practical_score', 5, 2)->default(0);
            $table->enum('top_modality', ['audio', 'visual', 'practical'])->nullable();
            $table->boolean('is_ambiguous')->default(false);
            $table->unsignedSmallInteger('data_points')->default(0);
            $table->timestamp('computed_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'term_id']);
            $table->index(['school_id', 'term_id', 'top_modality']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_modality_profiles');
    }
};