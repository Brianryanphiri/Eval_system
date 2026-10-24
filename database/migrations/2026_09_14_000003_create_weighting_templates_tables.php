<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Container: a named, reusable preset of assessment weights
        Schema::create('weighting_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['school_id', 'is_default']);
        });

        // Rows: one per assessment type inside a template
        Schema::create('weighting_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weighting_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('weight_percentage', 5, 2); // e.g. 30.00
            $table->timestamps();

            $table->unique(['weighting_template_id', 'assessment_type_id'], 'uniq_template_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weighting_template_items');
        Schema::dropIfExists('weighting_templates');
    }
};