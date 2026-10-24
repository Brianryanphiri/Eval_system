<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Teacher/head-teacher comments on the report card — Section 25
        Schema::create('report_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['class_teacher', 'head_teacher', 'subject_teacher']);
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete(); // set if subject_teacher
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_comments');
    }
};