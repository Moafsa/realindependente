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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2)->nullable();
            $table->json('features')->nullable();
            $table->integer('max_athletes')->nullable();
            $table->integer('max_teams')->nullable();
            $table->integer('max_branches')->nullable();
            $table->boolean('custom_domain')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('ai_features')->default(false);
            $table->decimal('ecommerce_tax_rate', 5, 2)->default(5.00);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
