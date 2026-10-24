<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // The base rule itself, one row per school
        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->enum('rule_type', [
                'overall_average',
                'all_compulsory',
                'min_passed',
                'credit_based',
                'board_decision',
                'custom',
            ]);
            $table->unsignedTinyInteger('min_overall_average')->nullable();
            $table->unsignedTinyInteger('max_failed_subjects')->nullable();
            $table->timestamps();
        });

        // Per-subject minimum conditions attached to a promotion rule
        // e.g. Mathematics >= 40%
        Schema::create('promotion_rule_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_rule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->enum('operator', ['>=', '>'])->default('>=');
            $table->unsignedTinyInteger('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_rule_conditions');
        Schema::dropIfExists('promotion_rules');
    }
};