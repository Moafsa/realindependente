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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // payment, refund, subscription, fee
            $table->string('status'); // pending, paid, failed, cancelled, refunded
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('BRL');
            $table->string('description');
            $table->json('metadata')->nullable(); // additional data
            $table->string('external_id')->nullable(); // Asaas payment ID
            $table->string('payment_method')->nullable(); // credit_card, boleto, pix
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('athlete_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
