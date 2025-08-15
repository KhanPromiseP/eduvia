<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ad_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->string('referrer')->nullable();
            $table->string('target_url');
            $table->datetime('clicked_at');
            $table->string('session_id')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->timestamps();

            $table->index(['ad_id', 'clicked_at']);
            $table->index('ip_address');
            $table->index('clicked_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_clicks');
    }
};