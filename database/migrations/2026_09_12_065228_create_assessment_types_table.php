<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // The assessment types a school selected in Wizard Step 2
        // (Continuous Assessment, Class Tests, Projects, etc.)
        Schema::create('assessment_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('key'); // 'continuous_assessment', 'mid_term', etc.
            $table->string('name'); // display label
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_types');
    }
};