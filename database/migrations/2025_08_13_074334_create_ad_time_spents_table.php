<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_time_spent', function (Blueprint $table) {
            $table->id();

            // Link to the ad
            $table->foreignId('ad_id')->constrained('ads', 'id')->onDelete('cascade')->name('fk_ad_time_spents_ad'); // <-- unique name to prevent duplicate error

            // Session & user tracking
            $table->string('session_id')->index();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // Total time spent in seconds
            $table->decimal('time_spent', 10, 2)->default(0);

            // Last tracked timestamp
            $table->timestamp('last_tracked_at')->nullable();

            $table->timestamps();

            // Unique per ad per session
            $table->unique(['ad_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_time_spent');
    }
};


