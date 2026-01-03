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
            'phone_number' => '0811111111',
        ]);

        \App\Models\User::create([
            'name' => 'CS Test',
            'email' => 'cs@test.com',
            'password' => bcrypt('password'),
            'role' => 'cs'
        ]);

        \App\Models\User::create([
            'name' => 'Zwar',
            'email' => 'zwarcs@cs.com',
            'password' => bcrypt('zwarcs123'),
            'role' => 'cs'
        ]);
    }
}
