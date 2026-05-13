<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the NOT NULL constraint and FK, re-add as nullable FK
        Schema::table('trainings', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['team_id']);
        });

        // Make column nullable via raw SQL (safest for PostgreSQL)
        DB::statement('ALTER TABLE trainings ALTER COLUMN team_id DROP NOT NULL');

        Schema::table('trainings', function (Blueprint $table) {
            // Re-add FK allowing null (no cascade delete since team can be null)
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });

        DB::statement('ALTER TABLE trainings ALTER COLUMN team_id SET NOT NULL');

        Schema::table('trainings', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }
};
