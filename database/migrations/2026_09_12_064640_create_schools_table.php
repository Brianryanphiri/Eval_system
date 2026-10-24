<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('institution_type', ['primary', 'secondary', 'international', 'college', 'custom']);
            $table->string('country')->default('Malawi');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->enum('term_structure', ['3_term', '2_semester', '4_quarter', 'custom'])->default('3_term');
            $table->boolean('setup_completed')->default(false);
            $table->tinyInteger('setup_step')->default(1); // tracks which wizard step they're on
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};