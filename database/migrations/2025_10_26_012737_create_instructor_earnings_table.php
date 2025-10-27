<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency');
            $table->text('reason');
            $table->integer('reason_code'); // Using Tranzak reason codes
            $table->string('status')->default('pending'); // pending, approved, rejected, processed
            $table->text('admin_notes')->nullable();
            $table->string('refund_id')->nullable(); // Tranzak refund ID
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('payment_id');
            $table->index('status');
            $table->index('refund_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('refund_requests');
    }
};