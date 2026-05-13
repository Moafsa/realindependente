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
        Schema::table('ai_generated_content', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('type');
            $table->date('start_date')->nullable()->after('status');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('frequency')->nullable()->after('end_date');
            $table->json('notification_settings')->nullable()->after('frequency');
            $table->string('goal')->nullable()->after('notification_settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_generated_content', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'start_date',
                'end_date',
                'frequency',
                'notification_settings',
                'goal'
            ]);
        });
    }
};
