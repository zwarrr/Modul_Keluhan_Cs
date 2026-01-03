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
        $sesi = \App\Models\Session::with(['member', 'cs'])->findOrFail($id);
        $pesans = \App\Models\Chat::where('session_id', $id)
            ->with(['senderMember', 'senderUser'])
            ->orderBy('sent_at', 'asc')
            ->get();
        
        $memberIdFromUsers = $sesi->member ? $sesi->member->member_id : $sesi->member_id;
        $displayName = $memberIdFromUsers . ' | ' . $sesi->id;
        
        $chatSesi = [
            'id' => $sesi->id,
            'member' => $displayName,
            'cs' => $sesi->cs ? $sesi->cs->name : '-',
            'status' => ucfirst($sesi->status),
            'last_message' => $sesi->last_message ?? '-',
            'last_activity' => $sesi->last_activity ? \Carbon\Carbon::parse($sesi->last_activity)->diffForHumans() : '-',
        ];

        $pesanList = $pesans->map(function($pesan) {
            $senderName = '-';
            $senderRole = 'member';
            
            if ($pesan->sender) {
                $senderName = $pesan->sender->name;
                // Cek role dari sender_type atau dari model
                if ($pesan->sender_type === 'cs') {
                    $senderRole = 'cs';
                } elseif (isset($pesan->sender->role)) {
                    $senderRole = $pesan->sender->role;
                }
            }
            
            return [
                'sender' => $senderName,
                'role' => $senderRole,
                'message' => $pesan->message,
                'time' => $pesan->sent_at ? \Carbon\Carbon::parse($pesan->sent_at)->format('H:i') : '',
            ];
        });

        return view('admin.sesi-chat.detail', [
            'id' => $id,
            'chatSesi' => $chatSesi,
            'pesans' => $pesanList
        ]);
    }

    /**
     * Get chat detail as JSON for modal view
     */
    public function apiDetail($id)
    {
        try {
            $sesi = \App\Models\Session::with(['member', 'cs'])->findOrFail($id);
            $pesans = \App\Models\Chat::where('session_id', $id)
                ->with(['senderMember', 'senderUser'])
                ->orderBy('sent_at', 'asc')
                ->get();
            
            $pesanList = $pesans->map(function($pesan) {
                $senderName = '-';
                $senderRole = 'member';
                
                if ($pesan->sender) {
                    $senderName = $pesan->sender->name;
                    if ($pesan->sender_type === 'cs') {
                        $senderRole = 'cs';
                    } elseif (isset($pesan->sender->role)) {
                        $senderRole = $pesan->sender->role;
                    }
                }
                
                return [
                    'sender' => $senderName,
                    'role' => $senderRole,
                    'message' => $pesan->message,
                    'time' => $pesan->sent_at ? \Carbon\Carbon::parse($pesan->sent_at)->format('H:i') : '',
                ];
            });

            return response()->json([
                'success' => true,
                'pesans' => $pesanList,
                'sesi' => [
                    'status' => $sesi->status,
                    'cs' => $sesi->cs ? $sesi->cs->name : 'Belum ditangani'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Chat tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Session::with(['member', 'cs', 'chats']);
        
        // Filter by CS
        if ($request->cs) {
            $query->where('cs_id', $request->cs);
        }
        
        // Filter by Member
        if ($request->member) {
            $query->where('member_id', $request->member);
        }
        
        // Filter by Status
        if ($request->status) {
            $statusMap = [
                'Open' => 'open',
                'Pending' => 'pending',
                'Closed' => 'closed'
            ];
            $query->where('status', $statusMap[$request->status] ?? $request->status);
        }
        
        // Filter by Period
        if ($request->periode) {
            switch ($request->periode) {
                case 'minggu':
                    $query->where('created_at', '>=', now()->startOfWeek());
                    break;
                case 'bulan':
                    $query->where('created_at', '>=', now()->startOfMonth());
                    break;
                case 'tahun':
                    $query->where('created_at', '>=', now()->startOfYear());
                    break;
            }
        }
        
        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('member', function($subQ) use ($request) {
                    $subQ->where('name', 'like', '%'.$request->search.'%');
                })->orWhere('last_message', 'like', '%'.$request->search.'%');
            });
        }
        
        // Sort
        $sort = $request->sort === 'asc' ? 'asc' : 'desc';
        $query->orderBy('last_activity', $sort);
        
        $sessions = $query->paginate(20);
        
        // Get list CS and Members for filter
        $listCs = \App\Models\User::where('role', 'cs')->get();
        $listMember = \App\Models\Member::all(); // Fix: ambil dari tabel members
        
        return view('admin.sesi-chat.index', compact('sessions', 'listCs', 'listMember'));
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



