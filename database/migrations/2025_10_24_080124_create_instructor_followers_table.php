<?php
// database/migrations/2024_01_01_create_instructor_followers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructorFollowersTable extends Migration
{
    public function up()
    {
        Schema::create('instructor_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['instructor_id', 'user_id']);
        });

        Schema::create('instructor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('rating');
            $table->text('review');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        Schema::create('instructor_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->text('response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('instructor_followers');
        Schema::dropIfExists('instructor_reviews');
        Schema::dropIfExists('instructor_messages');
    }
}