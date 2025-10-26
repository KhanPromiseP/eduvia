<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Enhanced video tracking - Rename to avoid conflict
        if (!Schema::hasTable('video_view_sessions')) {
            Schema::create('video_view_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('attachment_id')->constrained()->onDelete('cascade');
                $table->string('video_id')->index();
                $table->string('session_id')->unique();
                $table->string('ip_address', 45);
                $table->text('user_agent')->nullable();
                $table->decimal('watch_time', 8, 2)->default(0);
                $table->decimal('completion_rate', 5, 2)->default(0);
                $table->timestamp('started_at');
                $table->timestamp('ended_at')->nullable();
                $table->json('quality_changes')->nullable();
                $table->boolean('completed')->default(false);
                $table->timestamps();

                $table->index(['user_id', 'attachment_id']);
                $table->index(['session_id', 'ip_address']);
            });
        }

        // Offline access tracking
        if (!Schema::hasTable('offline_access')) {
            Schema::create('offline_access', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('attachment_id')->constrained()->onDelete('cascade');
                $table->string('device_id');
                $table->string('device_name')->nullable();
                $table->timestamp('expires_at');
                $table->string('access_token')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['user_id', 'attachment_id', 'device_id']);
                $table->index(['access_token', 'expires_at']);
            });
        }

        // Security events logging
        if (!Schema::hasTable('security_events')) {
            Schema::create('security_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('event_type'); // download_attempt, multiple_streams, etc.
                $table->string('ip_address', 45);
                $table->text('user_agent')->nullable();
                $table->json('event_data')->nullable();
                $table->timestamp('occurred_at');
                $table->timestamps();

                $table->index(['event_type', 'occurred_at']);
                $table->index(['user_id', 'event_type']);
            });
        }

        // Add secure columns to attachments if they don't exist
        if (Schema::hasTable('attachments')) {
            Schema::table('attachments', function (Blueprint $table) {
                if (!Schema::hasColumn('attachments', 'video_id')) {
                    $table->string('video_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('attachments', 'is_secure')) {
                    $table->boolean('is_secure')->default(false)->after('file_type');
                }
                if (!Schema::hasColumn('attachments', 'encryption_data')) {
                    $table->json('encryption_data')->nullable()->after('file_size');
                }
                if (!Schema::hasColumn('attachments', 'streaming_token')) {
                    $table->string('streaming_token')->nullable()->after('video_id');
                }
                
                $table->index(['video_id', 'is_secure']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('video_view_sessions');
        Schema::dropIfExists('offline_access');
        Schema::dropIfExists('security_events');
        
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropIndex(['video_id', 'is_secure']);
            $table->dropColumn(['video_id', 'is_secure', 'encryption_data', 'streaming_token']);
        });
    }
};