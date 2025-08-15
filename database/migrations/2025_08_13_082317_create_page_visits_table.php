<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->text('referrer')->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->datetime('visited_at');
            $table->string('session_id')->nullable();
            $table->timestamps();

            $table->index(['ip_address', 'visited_at']);
            $table->index('visited_at');
            $table->index('session_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('page_visits');
    }

};
