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
          Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained()->onDelete('cascade');
            $table->enum('event_type', ['impression', 'click', 'view']);
            $table->integer('duration')->default(0); // View duration in seconds
            $table->string('ip_address')->nullable(); // For geo-location
            $table->string('country')->nullable(); // Derived from IP
            $table->string('device_type')->nullable(); // e.g., 'mobile', 'desktop'
            $table->string('user_agent')->nullable(); // Raw for parsing
            $table->string('referrer')->nullable(); // Source
            $table->integer('value')->default(1); // Count or duration
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
