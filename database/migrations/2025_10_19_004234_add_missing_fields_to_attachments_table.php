<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            // Add missing fields
            if (!Schema::hasColumn('attachments', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            
            if (!Schema::hasColumn('attachments', 'allow_download')) {
                $table->boolean('allow_download')->default(true)->after('file_size');
            }
            
            if (!Schema::hasColumn('attachments', 'video_url')) {
                $table->string('video_url')->nullable()->after('file_path');
            }
            
            if (!Schema::hasColumn('attachments', 'thumbnail_url')) {
                $table->string('thumbnail_url')->nullable()->after('video_url');
            }
            
            if (!Schema::hasColumn('attachments', 'external_provider')) {
                $table->string('external_provider')->nullable()->after('thumbnail_url');
            }
            
            if (!Schema::hasColumn('attachments', 'external_video_id')) {
                $table->string('external_video_id')->nullable()->after('external_provider');
            }

            // Fix the cache table issue (if not already done)
            if (Schema::hasTable('cache')) {
                Schema::table('cache', function (Blueprint $table) {
                    $table->longText('value')->change();
                });
            }
        });
    }

    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'allow_download', 
                'video_url',
                'thumbnail_url',
                'external_provider',
                'external_video_id'
            ]);
        });
    }
};