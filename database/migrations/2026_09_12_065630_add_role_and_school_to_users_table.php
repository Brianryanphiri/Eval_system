<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->enum('role', [
                'super_admin',
                'school_admin',
                'principal',
                'academic_director',
                'head_of_department',
                'teacher',
                'student',
                'parent',
                'examiner',
            ])->default('teacher')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
            $table->dropColumn(['role', 'phone', 'last_login_at']);
        });
    }
};