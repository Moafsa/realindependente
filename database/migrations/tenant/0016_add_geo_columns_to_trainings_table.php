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
        Schema::table('trainings', function (Blueprint $table) {
            if (!Schema::hasColumn('trainings', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('location');
            }
            if (!Schema::hasColumn('trainings', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('trainings', 'address')) {
                $table->string('address')->nullable()->after('longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('trainings', 'latitude')) $columns[] = 'latitude';
            if (Schema::hasColumn('trainings', 'longitude')) $columns[] = 'longitude';
            if (Schema::hasColumn('trainings', 'address')) $columns[] = 'address';
            
            if (count($columns) > 0) {
                $table->dropColumn($columns);
            }
        });
    }
};
