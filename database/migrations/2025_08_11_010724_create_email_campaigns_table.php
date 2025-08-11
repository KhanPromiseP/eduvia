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
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Only you (admin)
            $table->foreignId('sequence_id')->nullable()->constrained('email_sequences')->onDelete('set null');
            $table->foreignId('group_id')->nullable()->constrained('subscriber_groups')->onDelete('set null');
            $table->string('title');
            $table->longText('content'); // HTML for images, links
            $table->timestamp('scheduled_at')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sent', 'failed'])->default('draft');
            $table->integer('delay_days')->default(0); // For sequences
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};
