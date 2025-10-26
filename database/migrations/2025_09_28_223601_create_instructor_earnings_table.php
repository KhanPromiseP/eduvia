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
        Schema::create('instructor_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->decimal('sale_amount', 10, 2); // total sale
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('affiliate_commission', 10, 2)->default(0);
            $table->decimal('instructor_share', 10, 2); // net earning
            $table->string('currency', 10)->default('USD');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_earnings');
    }
};
