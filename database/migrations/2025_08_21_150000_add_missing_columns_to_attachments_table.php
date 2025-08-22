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
        Schema::table('attachments', function (Blueprint $table) {
        $table->foreignId('module_id')->constrained('course_modules')->after('id')->onDelete('cascade');
        $table->string('title')->after('module_id');
        $table->string('file_path')->after('title');
        $table->string('file_type')->after('file_path');
        $table->integer('file_size')->nullable()->after('file_type');
        $table->integer('order')->default(0)->after('file_size');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
          Schema::table('attachments', function (Blueprint $table) {
        $table->dropForeign(['module_id']); // drop the foreign key first
        $table->dropColumn(['module_id', 'title', 'file_path', 'file_type', 'file_size', 'order']);
    });
    }
};
