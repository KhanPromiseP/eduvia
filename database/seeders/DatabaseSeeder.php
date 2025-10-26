<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   public function run(): void
    {
        // Create regular users first
        User::factory(10)->create();

        // Then create admin user (will be user #11)
        $this->call([
            RoleSeeder::class, 
            AdminUserSeeder::class,
        ]);

        // Then create other data
        $this->call([
            CourseSeeder::class,
            AdsTableSeeder::class,
        ]);
    }
}
