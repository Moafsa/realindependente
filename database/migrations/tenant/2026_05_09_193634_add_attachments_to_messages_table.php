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
        Schema::table('messages', function (Blueprint $dropdown) {
            $dropdown->string('attachment_path')->nullable()->after('content');
            $dropdown->string('attachment_type')->nullable()->after('attachment_path'); // image, video, document, audio, sticker
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $dropdown) {
            $dropdown->dropColumn(['attachment_path', 'attachment_type']);
        });
    }
};
