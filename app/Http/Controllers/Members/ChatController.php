<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        // Cari sesi aktif (open/pending) untuk member ini
        $sesi = \App\Models\Session::where('member_id', $request->member_id)
            ->whereIn('status', ['open', 'pending'])
            ->first();

        if (!$sesi) {
            // Jika belum ada, buat sesi baru
            $sesi = \App\Models\Session::create([
                'member_id' => $request->member_id,
                'cs_id' => null,
                'status' => 'pending',
                'last_message' => $request->message,
                'last_activity' => null,
            ]);
            
            // Broadcast session created event untuk realtime update di admin/CS
            broadcast(new \App\Events\SessionCreated([
                'id' => $sesi->id,
                'member_id' => $sesi->member_id,
                'status' => $sesi->status,
                'last_message' => $request->message,
                'cs_name' => null,
                'created_at' => $sesi->created_at->toIso8601String(),
            ]));
            
            \Log::info('[Session Created] Broadcasting new session', ['session_id' => $sesi->id]);
        } else {
            // Jika sudah ada, update last_message & last_activity
            $sesi->update([
                'last_message' => $request->message,
                'last_activity' => null,
            ]);
        }

        // Tambahkan pesan ke sesi
        $pesan = \App\Models\Chat::create([
            'session_id' => $sesi->id,
            'sender_id' => $request->member_id,
            'sender_type' => 'member',
            'message' => $request->message,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $sesi->id,
            'pesan_id' => $pesan->id,
        ]);
    }

    public function getSesi($id)
    {
        $sesi = \App\Models\Session::with(['member', 'cs', 'chats.senderMember', 'chats.senderUser'])
            ->findOrFail($id);

        $pesans = $sesi->chats->map(function($pesan) {
            return [
                'id' => $pesan->id,
                'message' => $pesan->message,
                'status' => $pesan->status,
                'sent_at' => $pesan->sent_at,
                'sender' => $pesan->sender ? [
                    'id' => $pesan->sender->id,
                    'name' => $pesan->sender->name,
                    'role' => $pesan->sender_type,
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
     * Display chat list for member
     */
    public function chatList()
    {
        $memberId = auth('member')->id();

        // Tampilkan 1 kontak saja (Customer Service). Sesi tetap disatukan di roomchat.
        $active = \App\Models\Session::with(['cs', 'member', 'lastChat'])
            ->withCount([
                'chats as unread_count' => function ($q) {
                    $q->where('sender_type', 'cs')->where('status', 'sent');
                }
            ])
            ->where('member_id', $memberId)
            ->whereIn('status', ['open', 'pending'])
            ->orderBy('created_at', 'desc')
            ->first();

        $latest = $active ?: \App\Models\Session::with(['cs', 'member', 'lastChat'])
            ->withCount([
                'chats as unread_count' => function ($q) {
                    $q->where('sender_type', 'cs')->where('status', 'sent');
                }
            ])
            ->where('member_id', $memberId)
            ->orderBy('last_activity', 'desc')
            ->first();

        // Jika tidak ada session sama sekali, buat session dummy untuk tampilkan kontak CS
        if (!$latest) {
            $cs = \App\Models\User::where('role', 'cs')->first();
            $member = auth('member')->user();
            
            // Buat object dummy untuk ditampilkan di view (belum disimpan ke database)
            $latest = (object) [
                'id' => null,
                'member_id' => $memberId,
                'cs_id' => $cs ? $cs->id : null,
                'status' => 'new',
                'last_message' => 'Mulai percakapan dengan Customer Service',
                'last_activity' => null,
                'cs' => $cs,
                'member' => $member,
                'lastChat' => null,
                'unread_count' => 0,
            ];
        }

        $sessions = collect([$latest]);

        return view('member.sections.chat_section', compact('sessions'));
    }

    /**
     * JSON endpoint for realtime chat list refresh (member only)
     */
    public function sessionsJson(Request $request)
    {
        $memberId = auth('member')->id();

        // 1 kontak saja (Customer Service): gunakan sesi aktif jika ada, jika tidak pakai sesi terakhir
        $active = \App\Models\Session::with(['cs', 'member', 'lastChat'])
            ->withCount([
                'chats as unread_count' => function ($q) {
                    $q->where('sender_type', 'cs')->where('status', 'sent');
                }
            ])
            ->where('member_id', $memberId)
            ->whereIn('status', ['open', 'pending'])
            ->orderBy('created_at', 'desc')
            ->first();

        $latest = $active ?: \App\Models\Session::with(['cs', 'member', 'lastChat'])
            ->withCount([
                'chats as unread_count' => function ($q) {
                    $q->where('sender_type', 'cs')->where('status', 'sent');
                }
            ])
            ->where('member_id', $memberId)
            ->orderBy('last_activity', 'desc')
            ->first();

        // Jika tidak ada session sama sekali, buat session dummy untuk tampilkan kontak CS
        if (!$latest) {
            $cs = \App\Models\User::where('role', 'cs')->first();
            $member = auth('member')->user();
            
            $latest = (object) [
                'id' => null,
                'member_id' => $memberId,
                'cs_id' => $cs ? $cs->id : null,
                'status' => 'new',
                'last_message' => 'Mulai percakapan dengan Customer Service',
                'last_activity' => null,
                'cs' => $cs,
                'member' => $member,
                'lastChat' => null,
                'unread_count' => 0,
            ];
        }

        $sessions = collect([$latest]);

        $payload = $sessions->map(function ($session) {
            $lastChat = $session->lastChat;
            $lastFromMember = $lastChat ? ($lastChat->sender_type === 'member') : false;
            $lastStatus = $lastChat ? ($lastChat->status ?? 'sent') : 'sent';

            $lastAt = null;
            if ($lastChat && $lastChat->sent_at) {
                $lastAt = Carbon::parse($lastChat->sent_at);
            } elseif ($session->last_activity) {
                $lastAt = Carbon::parse($session->last_activity);
            }

            $previewMessage = '';
            if ($lastChat) {
                $previewMessage = $lastChat->message;
                if (!$previewMessage && $lastChat->file_path) {
                    $previewMessage = ($lastChat->file_type === 'image') ? '[Image]' : '[File]';
                }
            } else {
                $previewMessage = $session->last_message ?? '';
            }

            // One-line preview (avoid newlines in list)
            $previewMessage = trim(preg_replace('/\s+/', ' ', (string) $previewMessage));

            return [
                'id' => $session->id,
                'name' => 'Customer Service',
                'message' => $previewMessage,
                // Frontend formats time display based on this ISO timestamp
                'time' => '',
                'last_activity' => $lastAt ? $lastAt->toIso8601String() : null,
                'avatar' => asset('img/logo_tms.png'),
                'unread' => (int) ($session->unread_count ?? 0),
                'status' => $session->status,
                'last_message_from_member' => $lastFromMember,
                'last_message_status' => $lastStatus,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'sessions' => $payload,
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




