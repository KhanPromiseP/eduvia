<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to change to LONGBLOB
        DB::statement('ALTER TABLE cache MODIFY value LONGBLOB');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE cache MODIFY value TEXT');
    }
};