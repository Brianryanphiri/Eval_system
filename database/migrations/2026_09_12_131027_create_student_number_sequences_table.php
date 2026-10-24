<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // One row per school per year — holds the "last number issued" counter.
        // Locked with lockForUpdate() at generation time to prevent race conditions.
        Schema::create('student_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();

            $table->unique(['school_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_number_sequences');
    }
};