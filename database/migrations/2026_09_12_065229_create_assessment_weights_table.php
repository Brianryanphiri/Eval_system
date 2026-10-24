<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Default school-wide weighting from Wizard Step 3
        // e.g. Continuous Assessment = 20%, Mid-Term = 20%, End-Term = 60%
        Schema::create('assessment_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_type_id')->constrained()->cascadeOnDelete();
            // nullable subject_id = school-wide default; set = subject-specific override (Section 43)
            $table->foreignId('subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('weight_percentage', 5, 2); // e.g. 30.00
            $table->timestamps();

            $table->unique(['school_id', 'assessment_type_id', 'subject_id'], 'uniq_weight_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_weights');
    }
};