<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_verified')->default(false); // If the user actually took the course
            $table->boolean('is_approved')->default(true); // For moderation
            $table->timestamps();

            $table->unique(['user_id', 'course_id']); // One review per user per course
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}