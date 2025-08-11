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
        Schema::create('subscriber_group_pivots', function (Blueprint $table) {
        $table->id();
        $table->foreignId('subscriber_id')->constrained('subscribers')->onDelete('cascade');
        $table->foreignId('group_id')->constrained('subscriber_groups')->onDelete('cascade'); 
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_group_pivots');
    }
};
