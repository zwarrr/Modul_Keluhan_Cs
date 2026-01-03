<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;
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
                'address' => 'Jl. Member Satu No. 1',
                'phone_number' => '081234567890',
            ],
            [
                'member_id' => '303030',
                'name' => 'Member Dua',
                'email' => null,
                'password' => Hash::make('303030'),
                'address' => 'Jl. Member Dua No. 2',
                'phone_number' => '081234567891',
            ],
            [
                'member_id' => '404040',
                'name' => 'Member Test',
                'email' => null,
                'password' => Hash::make('404040'),
                'address' => 'Jl. Testing No. 404',
                'phone_number' => '081234567892',
            ],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }
    }
}
