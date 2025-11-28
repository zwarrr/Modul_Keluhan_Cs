<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PesanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $member = \App\Models\User::where('role', 'member')->first();
        $cs = \App\Models\User::where('role', 'cs')->first();
        $sesi1 = \App\Models\Chat_sesi::first();
        $sesi2 = \App\Models\Chat_sesi::skip(1)->first();

        // Sesi 1 (Open)
        \App\Models\Pesan::create([
            'sesi_id' => $sesi1->id,
            'member_id' => $member->id,
            'message' => 'Selamat pagi, saya tidak bisa login ke akun saya.',
            'status' => 'sent',
            'sent_at' => now()->subMinutes(10),
        ]);
        \App\Models\Pesan::create([
            'sesi_id' => $sesi1->id,
            'member_id' => $cs->id,
            'message' => 'Selamat pagi, ada yang bisa kami bantu?',
            'status' => 'sent',
            'sent_at' => now()->subMinutes(9),
        ]);

        // Sesi 2 (Closed)
        \App\Models\Pesan::create([
            'sesi_id' => $sesi2->id,
            'member_id' => $member->id,
            'message' => 'Terima kasih atas bantuannya!',
            'status' => 'sent',
            'sent_at' => now()->subDay()->addMinutes(5),
        ]);
        \App\Models\Pesan::create([
            'sesi_id' => $sesi2->id,
            'member_id' => $cs->id,
            'message' => 'Sama-sama, silakan dicoba kembali.',
            'status' => 'sent',
            'sent_at' => now()->subDay()->addMinutes(6),
        ]);
    }
}
