<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->text('instructor_response')->nullable()->after('comment');
            $table->timestamp('response_date')->nullable()->after('instructor_response');
            $table->boolean('is_helpful')->default(false)->after('is_approved');
        });
    }

    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['instructor_response', 'response_date', 'is_helpful']);
        });
    }
};