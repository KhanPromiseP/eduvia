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
        
        $table->foreignId('ad_id')
            ->constrained()
            ->cascadeOnDelete();
        
        $table->string('session_id', 255);
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->float('time_spent', 10, 2)->default(0.00);
        $table->timestamp('last_tracked_at')->nullable();
        $table->string('placement', 50)->nullable();
        
        $table->timestamps();
        
        $table->unique(['ad_id', 'session_id']);
        $table->index(['ad_id', 'last_tracked_at']);
    });

    }

    public function down(): void
    {
        Schema::dropIfExists('ad_time_spent');
    }
};


