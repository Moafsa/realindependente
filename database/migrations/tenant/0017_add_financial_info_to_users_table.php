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
        Schema::table('users', function (Blueprint $col) {
            if (!Schema::hasColumn('users', 'salary')) {
                $col->decimal('salary', 10, 2)->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'payment_frequency')) {
                $col->string('payment_frequency')->nullable()->after('salary');
                // Options: training, hourly, daily, weekly, bi-weekly, monthly, etc.
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $col) {
            $col->dropColumn(['salary', 'payment_frequency']);
        });
    }
};
