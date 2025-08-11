<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder for creating an admin user.
 * To be run after the users table migration.
 */


class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'), 
            'email_verified_at' => now(),
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}