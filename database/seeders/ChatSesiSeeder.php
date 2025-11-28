<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatSesiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $member = \App\Models\User::where('role', 'member')->first();
        $cs = \App\Models\User::where('role', 'cs')->first();

        \App\Models\Chat_sesi::create([
            'member_id' => $member->id,
            'cs_id' => $cs->id,
            'status' => 'Open',
            'last_message' => 'Saya tidak bisa login ke akun saya.',
            'last_activity' => now(),
        ]);

        \App\Models\Chat_sesi::create([
            'member_id' => $member->id,
            'cs_id' => $cs->id,
            'status' => 'Closed',
            'last_message' => 'Terima kasih atas bantuannya!',
            'last_activity' => now()->subDay(),
            'closed_by' => $cs->id,
            'closed_at' => now()->subDay(),
        ]);
    }
}
