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
        Schema::table('performance_records', function (Blueprint $table) {
            $table->string('tenant_id')->nullable()->after('recorded_by');
            $table->string('tenant_name')->nullable()->after('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_records', function (Blueprint $table) {
            $table->dropColumn(['tenant_id', 'tenant_name']);
        });
    }
};
