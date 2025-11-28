<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
        /**
         * Endpoint REST API: Member mengirim pesan, otomatis create sesi & pesan
         * POST /api/member/chat/send
         * Body: { member_id, message }
         */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        // Cari sesi aktif (open/onprogress) untuk member ini
        $sesi = \App\Models\Chat_sesi::where('member_id', $request->member_id)
            ->whereIn('status', ['open', 'onprogress'])
            ->first();

        if (!$sesi) {
            // Jika belum ada, buat sesi baru
            $sesi = \App\Models\Chat_sesi::create([
                'member_id' => $request->member_id,
                'cs_id' => null,
                'status' => 'open',
                'last_message' => $request->message,
                'last_activity' => now(),
            ]);
        } else {
            // Jika sudah ada, update last_message & last_activity
            $sesi->update([
                'last_message' => $request->message,
                'last_activity' => now(),
            ]);
        }

        // Tambahkan pesan ke sesi
        $pesan = \App\Models\Pesan::create([
            'sesi_id' => $sesi->id,
            'member_id' => $request->member_id,
            'message' => $request->message,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'sesi_id' => $sesi->id,
            'pesan_id' => $pesan->id,
        ]);
    }

    public function getSesi($id)
    {
        $sesi = \App\Models\Chat_sesi::with(['member', 'cs', 'pesan.user'])
            ->findOrFail($id);

        $pesans = $sesi->pesan->map(function($pesan) {
            return [
                'id' => $pesan->id,
                'message' => $pesan->message,
                'status' => $pesan->status,
                'sent_at' => $pesan->sent_at,
                'sender' => $pesan->user ? [
                    'id' => $pesan->user->id,
                    'name' => $pesan->user->name,
                    'role' => $pesan->user->role,
                ] : null,
            ];
        });

        return response()->json([
            'id' => $sesi->id,
            'status' => $sesi->status,
            'last_message' => $sesi->last_message,
            'last_activity' => $sesi->last_activity,
            'member' => $sesi->member ? [
                'id' => $sesi->member->id,
                'name' => $sesi->member->name,
                'email' => $sesi->member->email,
            ] : null,
            'cs' => $sesi->cs ? [
                'id' => $sesi->cs->id,
                'name' => $sesi->cs->name,
                'email' => $sesi->cs->email,
            ] : null,
            'pesans' => $pesans,
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
