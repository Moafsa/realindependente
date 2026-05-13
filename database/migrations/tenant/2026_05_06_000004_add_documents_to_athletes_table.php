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
        Schema::table('athletes', function (Blueprint $table) {
            $table->string('athlete_document_path')->nullable();
            $table->string('residence_proof_path')->nullable();
            $table->string('guardian_document_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn([
                'athlete_document_path',
                'residence_proof_path',
                'guardian_document_path',
            ]);
        });
    }
};
