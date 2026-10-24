<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->enum('learning_track', ['audio', 'visual', 'practical', 'mixed'])
                ->nullable()
                ->after('grade_level');
            $table->index(['school_id', 'grade_level', 'learning_track']);
        });

        DB::table('classes')->orderBy('id')->get(['id', 'name'])->each(function ($class) {
            $name = trim($class->name);

            if (!preg_match('/^form\s*\d+\s*([a-e])$/i', $name, $m)) {
                return;
            }

            $track = match (strtoupper($m[1])) {
                'A' => 'audio',
                'B' => 'visual',
                default => 'practical',
            };

            DB::table('classes')->where('id', $class->id)->update(['learning_track' => $track]);
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex(['school_id', 'grade_level', 'learning_track']);
            $table->dropColumn('learning_track');
        });
    }
};