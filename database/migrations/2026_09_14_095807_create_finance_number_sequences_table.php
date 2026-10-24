<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // One row per school, per year, per number type (invoice/receipt), same locking pattern as student numbers
        Schema::create('finance_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->enum('type', ['invoice', 'receipt']);
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();

            $table->unique(['school_id', 'year', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_number_sequences');
    }
};