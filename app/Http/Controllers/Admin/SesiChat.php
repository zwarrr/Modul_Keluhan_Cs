<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SesiChat extends Controller
{
    /**
     * Show the detail of a chat session.
     */
    public function detail($id)
    {
        // Data dummy chat session
        $chatSesi = [
            'id' => $id,
            'member' => 'Alexander Pierce',
            'cs' => 'Sarah Connor',
            'status' => 'Open',
            'last_message' => 'Trying to find a solution to this problem...',
            'last_activity' => '5 menit lalu',
        ];

        $pesans = [
            [
                'sender' => 'Alexander Pierce',
                'role' => 'member',
                'message' => 'Halo, saya butuh bantuan.',
                'time' => '10:00',
            ],
            [
                'sender' => 'Sarah Connor',
                'role' => 'cs',
                'message' => 'Halo, ada yang bisa saya bantu?',
                'time' => '10:01',
            ],
            [
                'sender' => 'Alexander Pierce',
                'role' => 'member',
                'message' => 'Saya mengalami kendala login.',
                'time' => '10:02',
            ],
            [
                'sender' => 'Sarah Connor',
                'role' => 'cs',
                'message' => 'Baik, akan saya cek dulu ya.',
                'time' => '10:03',
            ],
        ];

        return view('admin.sesi-chat.detail', compact('id', 'chatSesi', 'pesans'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.sesi-chat.index');
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
