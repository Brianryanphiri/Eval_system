<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // The master list of permission keys — results.view, results.enter, etc.
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'results.enter'
            $table->string('label'); // 'Enter Results'
            $table->string('group'); // 'Results', 'Assessments', etc. — for the matrix UI grouping
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};