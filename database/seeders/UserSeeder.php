<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => '1234',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Student',
            'email' => 'student@gmail.com',
            'password' => '1234',
            'role' => 'student',
        ]);
    }
}
