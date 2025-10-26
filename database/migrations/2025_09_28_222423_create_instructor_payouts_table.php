<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('instructor_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->enum('payment_method',['paypal','bank','momo']);
            $table->string('payment_email')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('momo_number')->nullable();
            $table->string('currency',10)->default('USD');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_payouts');
    }
};
