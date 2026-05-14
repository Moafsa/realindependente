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
        Schema::table('plans', function (Blueprint $table) {
            $table->decimal('price_quarterly', 10, 2)->nullable();
            $table->decimal('price_semiannual', 10, 2)->nullable();
            $table->integer('discount_quarterly')->default(0);
            $table->integer('discount_semiannual')->default(0);
            $table->integer('discount_yearly')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'price_quarterly',
                'price_semiannual',
                'discount_quarterly',
                'discount_semiannual',
                'discount_yearly'
            ]);
        });
    }
};
