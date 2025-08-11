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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Admin who created the product (you)
            $table->string('title'); // Product name (e.g., "Digital Marketing Course")
            $table->text('description')->nullable(); // Detailed description
            $table->string('thumbnail')->nullable(); // URL or path to thumbnail image
            $table->decimal('price', 10, 2); // Price of the digital product
            $table->string('file_path')->nullable(); // URL or path to the digital file (e.g., PDF, video)
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft'); // Product status
            $table->boolean('is_active')->default(true); // Whether product is available for purchase
            $table->json('metadata')->nullable(); // Extra info (e.g., {"duration": "2 hours", "format": "PDF"})
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
