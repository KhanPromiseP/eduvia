<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('headline')->nullable();
            $table->longText('bio')->nullable();
            $table->json('languages')->nullable(); // ["English","French"]
            $table->json('skills')->nullable();    // ["Laravel","React"]
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_students')->default(0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};
