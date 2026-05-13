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
        Schema::table('products', function (Blueprint $table) {
            // Rename category to type if it exists
            if (Schema::hasColumn('products', 'category')) {
                $table->renameColumn('category', 'type');
            } else if (!Schema::hasColumn('products', 'type')) {
                $table->string('type')->default('product')->after('price');
            }

            // Add missing columns
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('sku');
            }

            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('image');
            }

            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }

            // Make slug and sku nullable if they exist
            $table->string('slug')->nullable()->change();
            $table->string('sku')->nullable()->change();
            
            // Add soft deletes if missing
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }
            
            // Fix dimensions if it was json but code expects string
            $table->string('dimensions')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Rollback changes if needed
        });
    }
};
