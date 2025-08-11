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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Creator (admin or normal user for paid ads)
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('title');
            $table->enum('type', ['image', 'video', 'banner', 'js', 'popup', 'persistent', 'interstitial']);
            $table->text('content'); // URL/path for media or JS code
            $table->string('link')->nullable(); // Product/external/payment page
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('placement')->nullable(); // e.g., 'header', 'sidebar', 'specific-page:/about', 'random'
            $table->json('targeting')->nullable(); // e.g., {"devices": ["mobile"], "countries": ["US"], "locations": ["sitewide"]}
            $table->boolean('is_random')->default(false); // Random placement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
