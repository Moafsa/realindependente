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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->date('birth_date');
            $table->string('position'); // goalkeeper, defender, midfielder, forward
            $table->string('profile_picture_url')->nullable();
            $table->text('bio')->nullable();
            $table->string('jersey_number', 5)->nullable();
            $table->decimal('height', 5, 2)->nullable(); // in m
            $table->decimal('weight', 5, 2)->nullable(); // in kg
            $table->string('emergency_contact')->nullable();
            $table->json('medical_conditions')->nullable();
            $table->json('allergies')->nullable();
            $table->string('insurance_info')->nullable();
            
            // Guardian Info
            $table->string('guardian_name')->nullable();
            $table->string('guardian_contact')->nullable();
            $table->string('guardian_email')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
