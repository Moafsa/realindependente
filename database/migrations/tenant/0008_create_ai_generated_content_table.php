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
        Schema::create('ai_generated_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['meal_plan', 'workout_plan']);
            $table->json('content');
            $table->text('prompt');
            $table->string('model_used')->default('gpt-4');
            $table->integer('tokens_used')->nullable();
            $table->decimal('cost', 8, 4)->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_generated_content');
    }
};
