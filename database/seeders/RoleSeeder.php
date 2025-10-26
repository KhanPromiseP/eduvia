<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            ['name' => 'admin', 'description' => 'Full platform control'],
            ['name' => 'instructor', 'description' => 'Can create and manage courses'],
            ['name' => 'student', 'description' => 'Can purchase and take courses'],
        ]);
    }
}
