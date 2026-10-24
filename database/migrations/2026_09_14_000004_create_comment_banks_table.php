<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comment_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('category');        // 'strength', 'weakness', 'general', 'effort'
            $table->string('band')->nullable(); // 'excellent', 'good', 'average', 'poor' — nullable = any
            $table->string('subject')->nullable(); // 'Mathematics', null = all subjects
            $table->text('text');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['school_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_banks');
    }
};