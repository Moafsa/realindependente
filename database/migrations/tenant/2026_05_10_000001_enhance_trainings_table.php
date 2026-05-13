<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            if (!Schema::hasColumn('trainings', 'type')) {
                $table->string('type')->default('training')->after('team_id'); // training, match, event
            }
            if (!Schema::hasColumn('trainings', 'status')) {
                $table->string('status')->default('scheduled')->after('location'); // scheduled, completed, cancelled
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn(['type', 'status']);
        });
    }
};
