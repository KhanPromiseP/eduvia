<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('user_progress') && !Schema::hasColumn('user_progress', 'completion_percentage')) {
            Schema::table('user_progress', function (Blueprint $table) {
                $table->decimal('completion_percentage', 5, 2)->default(0)->after('completed');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('user_progress') && Schema::hasColumn('user_progress', 'completion_percentage')) {
            Schema::table('user_progress', function (Blueprint $table) {
                $table->dropColumn('completion_percentage');
            });
        }
    }
};