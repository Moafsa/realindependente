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
        Schema::create('ai_plans', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // training, nutrition, recovery
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content'); // AI generated content
            $table->json('parameters')->nullable(); // input parameters for AI
            $table->json('goals')->nullable(); // athlete goals
            $table->json('restrictions')->nullable(); // dietary, physical restrictions
            $table->string('status')->default('active'); // active, completed, archived
            $table->integer('duration_weeks')->default(4);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('rating')->nullable(); // 1-5 stars
            $table->text('feedback')->nullable();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_plans');
    }
};
