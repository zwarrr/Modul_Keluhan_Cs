<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $members = [
            [
                'member_id' => '202020',
                'name' => 'Member Satu',
                'email' => null,
                'password' => Hash::make('202020'),
                'role' => 'member',
                'phone_number' => '081234567890',
            ],
            [
                'member_id' => '303030',
                'name' => 'Member Dua',
                'email' => null,
                'password' => Hash::make('303030'),
                'role' => 'member',
                'phone_number' => '081234567891',
            ],
        ];

        foreach ($members as $member) {
            User::create($member);
        }
    }
}
