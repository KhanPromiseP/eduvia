<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_encryption_keys', function (Blueprint $table) {
            $table->id();
            $table->string('video_id')->unique();
            $table->text('encryption_key'); // Base64 encoded
            $table->timestamps();
            
            $table->index('video_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_encryption_keys');
    }
};