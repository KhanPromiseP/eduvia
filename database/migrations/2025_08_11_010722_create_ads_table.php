<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->index();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');


            // Main ad details
            $table->string('title');
            $table->string('type')->index(); // e.g., banner, video, text
            $table->text('content')->nullable();
            $table->string('link')->nullable();

            // Scheduling
            $table->timestamp('start_at')->nullable()->index();
            $table->timestamp('end_at')->nullable()->index();

            // Status & placement
            $table->boolean('is_active')->default(true)->index();
            $table->string('placement')->nullable()->index();
            $table->json('targeting')->nullable();

            // Randomization & priorities
            $table->boolean('is_random')->default(false)->index();
            $table->integer('weight')->default(1);

            // Budget & limits
            $table->decimal('budget', 10, 2)->nullable();
            $table->integer('max_impressions')->nullable();
            $table->integer('max_clicks')->nullable();

            // Laravel timestamps & soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Commonly queried combos for performance
            $table->index(['is_active', 'start_at', 'end_at']);
            $table->index(['placement', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
