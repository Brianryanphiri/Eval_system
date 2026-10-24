<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subject_modality_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->enum('primary_modality', ['audio', 'visual', 'practical']);
            $table->enum('secondary_modality', ['audio', 'visual', 'practical'])->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_modality_map');
    }
};