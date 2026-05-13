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
        Schema::create('posts', function (Blueprint $column) {
            $column->id();
            $column->string('title');
            $column->string('slug')->unique();
            $column->text('content');
            $column->text('excerpt')->nullable();
            $column->string('image_url')->nullable();
            $column->string('status')->default('pending_approval'); // draft, pending_approval, scheduled, published
            $column->timestamp('scheduled_at')->nullable();
            $column->timestamp('published_at')->nullable();
            $column->string('meta_description')->nullable();
            $column->string('ai_model')->nullable();
            $column->integer('tokens_used')->nullable();
            $column->timestamps();
            $column->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
