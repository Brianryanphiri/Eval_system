<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('placement_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->enum('current_class', ['A', 'B', 'C'])->nullable();
            $table->enum('recommended_class', ['A', 'B', 'C'])->nullable();
            $table->json('reason_codes');
            $table->json('corrective_actions');
            $table->enum('status', ['pending', 'applied', 'dismissed', 'superseded'])
                ->default('pending');
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'term_id', 'status']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_recommendations');
    }
};