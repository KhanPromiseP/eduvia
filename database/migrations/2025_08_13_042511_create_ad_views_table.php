<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ad_views', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ad_id')->constrained()->onDelete('cascade');
        $table->string('ip_address', 45);
        $table->text('user_agent');
        $table->string('referrer')->nullable();
        $table->string('url');
        $table->integer('viewport_width')->nullable();
        $table->integer('viewport_height')->nullable();
        $table->datetime('viewed_at');
        $table->string('session_id'); // Changed from nullable to required
        $table->string('country', 2)->nullable();
        $table->string('city')->nullable();
        $table->string('placement', 50)->nullable(); // ADD THIS COLUMN
        $table->timestamps();

        $table->index(['ad_id', 'viewed_at']);
        $table->index('ip_address');
        $table->index('viewed_at');
        $table->index('session_id'); // Add index for session_id
});

        
    }

    public function down()
    {
        Schema::dropIfExists('ad_views');
    }
};