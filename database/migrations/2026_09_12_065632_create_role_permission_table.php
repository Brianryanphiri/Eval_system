<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Which roles have which permissions, per school (so School A can customize
        // its Teacher role's permissions independently of School B)
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('role'); // matches the users.role enum values
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'role', 'permission_id'], 'uniq_role_permission');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
    }
};