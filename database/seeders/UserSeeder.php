<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'address' => 'Jl. Admin',
            'phone_number' => '0811111111',
        ]);

        \App\Models\User::create([
            'name' => 'CS Test',
            'email' => 'cs@test.com',
            'password' => bcrypt('password'),
            'role' => 'cs',
            'address' => 'Jl. CS',
            'phone_number' => '0822222222',
        ]);

        \App\Models\User::create([
            'name' => 'Member Test',
            'email' => 'member@test.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'address' => 'Jl. Member',
            'phone_number' => '0833333333',
        ]);
    }
}
