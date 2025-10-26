<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Video progress tracking (for real-time progress)
        if (!Schema::hasTable('video_progress')) {
            Schema::create('video_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('attachment_id')->constrained()->onDelete('cascade');
                $table->decimal('current_time', 10, 3)->default(0);
                $table->decimal('total_duration', 10, 3)->default(0);
                $table->decimal('progress_percentage', 5, 2)->default(0);
                $table->decimal('total_watched_time', 10, 2)->default(0);
                $table->boolean('completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('last_watched_at')->nullable();
                $table->string('session_id')->nullable();
                $table->string('quality')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'attachment_id']);
                $table->index(['user_id', 'completed']);
                $table->index(['attachment_id', 'completed']);
            });
        }

        // Video events tracking (for analytics)
        if (!Schema::hasTable('video_events')) {
            Schema::create('video_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('attachment_id')->constrained()->onDelete('cascade');
                $table->string('session_id');
                $table->string('event_type'); // play, pause, ended, seeking, etc.
                $table->decimal('current_time', 10, 3)->nullable();
                $table->string('quality')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('ip_address', 45);
                $table->timestamp('occurred_at');
                $table->timestamps();

                $table->index(['user_id', 'attachment_id']);
                $table->index(['session_id', 'event_type']);
                $table->index('occurred_at');
            });
        }

        // Module progress tracking
        if (!Schema::hasTable('module_progress')) {
            Schema::create('module_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_module_id')->constrained()->onDelete('cascade');
                $table->integer('completed_attachments')->default(0);
                $table->integer('total_attachments')->default(0);
                $table->decimal('progress_percentage', 5, 2)->default(0);
                $table->boolean('completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('last_accessed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'course_module_id']);
                $table->index(['user_id', 'completed']);
            });
        }

        // Course progress tracking
        if (!Schema::hasTable('course_progress')) {
            Schema::create('course_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->integer('completed_modules')->default(0);
                $table->integer('total_modules')->default(0);
                $table->decimal('progress_percentage', 5, 2)->default(0);
                $table->boolean('completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('last_accessed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'course_id']);
                $table->index(['user_id', 'completed']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('video_progress');
        Schema::dropIfExists('video_events');
        Schema::dropIfExists('module_progress');
        Schema::dropIfExists('course_progress');
    }
};