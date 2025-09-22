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
        Schema::table('attachments', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->string('video_url')->nullable()->after('file_size');
            $table->string('thumbnail_url')->nullable()->after('video_url');
            
            // Make file_path nullable to support external videos
            $table->string('file_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn(['description', 'video_url', 'thumbnail_url']);
            $table->string('file_path')->nullable(false)->change();
        });
    }
};