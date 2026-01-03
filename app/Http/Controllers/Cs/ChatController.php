<?php

namespace App\Http\Controllers\Cs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\Chat;
use App\Models\User;
use App\Events\UserTyping;
use App\Events\MessageSent;
use App\Events\MessagesRead;
use App\Events\SessionClosed;

class ChatController extends Controller
{
        /**
         * CS mengirim Chat ke sesi tertentu
         * POST /cs/chat/{id}/send
         * Body: { message }
         */
        public function sendMessage(Request $request, $id)
        {
            $request->validate([
                'message' => 'required|string',
            ]);

            // Only CS can send messages, NOT admin (view only)
            $cs = null;
            if (auth('cs')->check()) {
                $cs = auth('cs')->user();
            } elseif (auth('admin')->check()) {
                \Log::warning('Send message blocked: Admin cannot send messages (view only)', [
                    'session_id' => $id,
                    'admin_id' => auth('admin')->id()
                ]);
                return response()->json(['success' => false, 'message' => 'Admin tidak dapat mengirim pesan (view only)'], 403);
            } else {
                abort(401, 'Unauthorized');
            }
            
            if (!$cs) {
                abort(401, 'Unauthorized');
            }
            
            $sesi = Session::findOrFail($id);

            // Jika sesi belum ada CS, update cs_id ke user yang sedang handle
            if (!$sesi->cs_id) {
                $sesi->cs_id = $cs->id;
            }
            
            // Jika session closed, buka kembali saat CS kirim pesan
            if ($sesi->status === 'closed') {
                $sesi->status = 'pending'; // Reopen as pending
            }
            
            $sesi->last_message = $request->message;
            $sesi->last_activity = now();
            $sesi->save();

            // Buat Chat baru dari CS
            $Chat = Chat::create([
                'session_id' => $sesi->id,
                'sender_id' => $cs->id,
                'sender_type' => 'cs',
                'message' => $request->message,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            
            // Broadcast Chat via WebSocket
            \Log::info('[CS Broadcast] Sending message to channel chat.' . $sesi->id, [
                'session_id' => $sesi->id,
                'message' => $request->message,
                'cs_id' => $cs->id,
                'cs_name' => $cs->name,
            ]);
            
            broadcast(new MessageSent(
                $request->message,
                $sesi->id,
                $cs->id,
                $cs->name,
                'cs',
                null,
                null,
                now()->format('H:i'),
                'sent',
                $Chat->id
            ));
            
            \Log::info('[CS Broadcast] Message broadcasted successfully');

            return response()->json([
                'success' => true,
                'pesan_id' => $Chat->id,
                'message_id' => $Chat->id,
            ]);
        }
    public function index(Request $request)
    {
        // Detect guard STRICTLY based on authenticated session
        // Middleware sudah handle auth, jadi tinggal check guard mana yang aktif
        $currentUser = null;
        $guardUsed = null;
        
        if (auth('cs')->check()) {
            $currentUser = auth('cs')->user();
            $guardUsed = 'cs';
        } elseif (auth('admin')->check()) {
            $currentUser = auth('admin')->user();
            $guardUsed = 'admin';
        } else {
            abort(401, 'Unauthorized - No guard authenticated');
        }
        
        $cs = $currentUser;

        $query = Session::with(['member', 'chats.senderMember', 'chats.senderUser']);

        // Filter berdasarkan parameter ?filter=my-chats
        if ($request->get('filter') === 'my-chats') {
            // Chat Saya: Hanya chat yang sudah ditangani oleh user ini (CS atau Admin)
            $query->where('cs_id', $cs->id);
        } else {
            // Semua Chat: 
            // - Untuk CS: Hanya chat yang belum ditangani (cs_id null) DAN status open/pending
            // - Untuk Admin: Semua chat yang belum ditangani (cs_id null) atau chat yang masih open/pending
            if ($currentUser->role === 'cs') {
                $query->whereNull('cs_id')
                      ->whereIn('status', ['open', 'pending']);
            } else if ($currentUser->role === 'admin') {
                // Admin: Tampilkan yang belum ditangani ATAU yang masih aktif (open/pending)
                $query->where(function($q) {
                    $q->whereNull('cs_id')
                      ->orWhereIn('status', ['open', 'pending']);
                });
            }
        }

        // Filter search
        if ($request->search) {
            $query->whereHas('member', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            });
        }


        $sessions = $query->get();

        // Debug: Log sessions untuk troubleshooting
        \Log::info('[CS Chat Index] Guard: ' . $guardUsed . ', User: ' . $cs->id . ' (' . $currentUser->role . '), Filter: ' . ($request->get('filter') ?? 'all') . ', Total: ' . $sessions->count());
        
        // Group sessions by member so CS list doesn't create "new contacts" per session
        $grouped = $sessions->groupBy('member_id');

        $chatList = $grouped->map(function ($memberSessions) use ($cs, $currentUser) {
            // Debug each group
            \Log::info('[CS Chat Index] Member ' . ($memberSessions->first()->member_id ?? 'unknown') . ' has ' . $memberSessions->count() . ' sessions');
            foreach ($memberSessions as $s) {
                \Log::info('  - Session #' . $s->id . ': cs_id=' . ($s->cs_id ?? 'null') . ', status=' . $s->status);
            }
            
            // Pick representative session: active (open/pending) preferred, otherwise latest by last_activity
            $active = $memberSessions
                ->whereIn('status', ['open', 'pending'])
                ->sortByDesc(function ($s) { return $s->last_activity ?: $s->created_at; })
                ->first();

            $rep = $active ?: $memberSessions
                ->sortByDesc(function ($s) { return $s->last_activity ?: $s->created_at; })
                ->first();

            // Compute last message across all sessions
            $lastPesan = $memberSessions
                ->flatMap(function ($s) { return $s->chats; })
                ->sortByDesc('sent_at')
                ->first();

            $unread = $memberSessions
                ->flatMap(function ($s) { return $s->chats; })
                ->filter(function ($p) {
                    return $p->sender_type === 'member' && $p->is_read == false;
                })
                ->count();

            $memberIdFromUsers = ($rep && $rep->member) ? $rep->member->member_id : ($rep ? $rep->member_id : '-');
            $displayName = (string) $memberIdFromUsers;

            $lastAt = $lastPesan && $lastPesan->sent_at ? \Carbon\Carbon::parse($lastPesan->sent_at) : null;

            // Status shown in list: show active status if any, else closed
            $status = $active ? $active->status : ($rep ? $rep->status : 'closed');

            return [
                // Keep linking to a session id so the detail route still works
                'id' => $rep ? $rep->id : null,
                'member_id' => $rep ? $rep->member_id : null,
                'name' => $displayName,
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=random&color=fff',
                'last_message' => $lastPesan ? ($lastPesan->message ?? '-') : '-',
                'last_time' => $lastAt ? $lastAt->format('H:i') : '',
                'unread' => $unread,
                'status' => $status,
                'last_activity' => $lastAt ? $lastAt->toIso8601String() : null,
            ];
        })->values();

        // Filter unread/read at member level
        if ($request->read === 'unread') {
            $chatList = $chatList->filter(function ($row) { return (int) ($row['unread'] ?? 0) > 0; })->values();
        } elseif ($request->read === 'read') {
            $chatList = $chatList->filter(function ($row) { return (int) ($row['unread'] ?? 0) === 0; })->values();
        }

        // Sorting at member level
        $sort = $request->sort === 'asc' ? 'asc' : 'desc';
        $chatList = $chatList->sortBy(function ($row) {
            return $row['last_activity'] ?: '';
        }, SORT_REGULAR, $sort === 'desc')->values();

        if ($request->ajax()) {
            return response()->json(['data' => $chatList]);
        }

        return view('cs.chat.index', compact('chatList'));
    }
    public function detail($id)
    {
        // Detect current user role.
        // IMPORTANT: admin and cs guards can both be authenticated in the same browser.
        // For /admin/* routes, ALWAYS treat as admin (view-only) even if cs guard is active.
        $isAdminRoute = request()->is('admin/*') || request()->routeIs('admin.*');

        $currentUser = null;
        if ($isAdminRoute) {
            if (!auth('admin')->check()) {
                abort(401, 'Unauthorized');
            }
            $currentUser = auth('admin')->user();
        } else {
            if (auth('cs')->check()) {
                $currentUser = auth('cs')->user();
            } elseif (auth('admin')->check()) {
                // Fallback: allow admin to view CS route if they somehow land here
                $currentUser = auth('admin')->user();
            } else {
                abort(401, 'Unauthorized');
            }
        }
        
        // Ambil data sesi dan Chat berdasarkan id
        $sesi = \App\Models\Session::with('member')->findOrFail($id);
        
        // Update status Chat member menjadi 'read' hanya jika user adalah CS (NOT admin - view only)
        $updated = 0;
        if ($currentUser->role === 'cs') {
            $updated = \App\Models\Chat::where('session_id', $id)
                ->where('sender_type', 'member')
                ->where('status', 'sent')
                ->update([
                    'status' => 'read',
                    'is_read' => true
                ]);
            
            // Broadcast messages read event jika ada yang di-update
            if ($updated > 0) {
                \Log::info('Broadcasting MessagesRead for sesi: ' . $id . ', updated: ' . $updated);
                broadcast(new MessagesRead($id));
            }
        } else {
            \Log::info('Skipping mark as read for admin (view only) on sesi: ' . $id);
        }
        
        // Load all sessions for this member (continuous room)
        $memberSessionsAsc = \App\Models\Session::where('member_id', $sesi->member_id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Build session index (Sesi 1..N) in chronological order
        $sessionIndexMap = [];
        $i = 1;
        foreach ($memberSessionsAsc as $ms) {
            $sessionIndexMap[(string) $ms->id] = $i;
            $i++;
        }

        $items = collect();
        foreach ($memberSessionsAsc as $session) {
            $pesans = \App\Models\Chat::where('session_id', $session->id)
                ->with(['senderMember', 'senderUser'])
                ->orderBy('sent_at', 'asc')
                ->get();

            // Session start marker (per-session badge)
            if ($session->status === 'closed') {
                $idx = $sessionIndexMap[(string) $session->id] ?? null;
                $badgeDate = $session->closed_at ?: ($session->last_activity ?: $session->created_at);
                $labelDate = $badgeDate ? \Carbon\Carbon::parse($badgeDate)->format('d-m-Y') : '';
                $items->push([
                    'type' => 'session_badge',
                    'session_id' => $session->id,
                    'label' => 'Sesi ' . ($idx ?? '-') . ($labelDate ? ' | ' . $labelDate : ''),
                ]);
            } else {
                $startAt = null;
                if ($pesans->count() > 0 && $pesans->first()->sent_at) {
                    $startAt = \Carbon\Carbon::parse($pesans->first()->sent_at);
                } else {
                    $startAt = \Carbon\Carbon::parse($session->created_at);
                }

                $label = $startAt->format('d-m-Y');
                if ($startAt->isToday()) {
                    $label = 'Today';
                } elseif ($startAt->isYesterday()) {
                    $label = 'Yesterday';
                }

                $items->push([
                    'type' => 'date_badge',
                    'session_id' => $session->id,
                    'label' => $label,
                ]);
            }

            foreach ($pesans as $Chat) {
                $items->push([
                    'type' => 'message',
                    'sender' => $Chat->sender ? $Chat->sender->name : '-',
                    'role' => $Chat->sender_type,
                    'message' => $Chat->message,
                    'file_path' => $Chat->file_path ? asset('storage/' . $Chat->file_path) : null,
                    'file_type' => $Chat->file_type,
                    'time' => $Chat->sent_at ? (\Carbon\Carbon::parse($Chat->sent_at)->format('H:i')) : '',
                    'session_id' => $session->id,
                ]);
            }
        }

        // Format untuk view/JSON
        $memberIdFromUsers = $sesi->member ? $sesi->member->member_id : $sesi->member_id;
        $displayName = $memberIdFromUsers . ' | ' . $sesi->id;
        
        $chatSesi = [
            'id' => $sesi->id,
            'member' => $displayName,
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=random&color=fff',
            'status' => $sesi->status,
            'cs_id' => $sesi->cs_id,
        ];
        $pesanList = $items->values();

        // Jika AJAX polling, return partial HTML Chat saja
        if (request('ajax')) {
            $html = view('cs.chat._messages', ['pesans' => $pesanList])->render();
            return response($html);
        }

        // Detect route prefix based on guard
        $routePrefix = ($currentUser && $currentUser->role === 'admin') ? 'admin.cs.chat' : 'cs.chat';

        return view('cs.chat.detail', [
            'chatSesi' => $chatSesi,
            'pesans' => $pesanList,
            'routePrefix' => $routePrefix,
            // Important: don't rely on Blade auth() default guard in CS/Admin area
            'currentUserId' => $currentUser ? $currentUser->id : null,
            'currentUserRole' => $currentUser ? $currentUser->role : null,
            'isViewOnly' => ($currentUser && $currentUser->role === 'admin'),
        ]);
    }

        /**
     * Tutup sesi chat (update status jadi closed)
     * POST /cs/chat/{id}/close
     */
    public function closeSession(Request $request, $id)
    {
        // Only CS can close sessions, NOT admin (view only)
        $cs = null;
        if (auth('cs')->check()) {
            $cs = auth('cs')->user();
        } elseif (auth('admin')->check()) {
            \Log::warning('Close session blocked: Admin cannot close sessions (view only)', [
                'session_id' => $id,
                'admin_id' => auth('admin')->id()
            ]);
            return response()->json(['success' => false, 'message' => 'Admin tidak dapat menutup sesi (view only)'], 403);
        } else {
            abort(401, 'Unauthorized');
        }
        
        if (!$cs) {
            abort(401, 'Unauthorized');
        }
        
        $sesi = \App\Models\Session::findOrFail($id);
        
        // Kirim Chat penutup otomatis dari CS/Admin yang menutup
        $closeMessage = Chat::create([
            'session_id' => $sesi->id,
            'sender_id' => $cs->id,
            'sender_type' => 'cs',
            'message' => 'Sesi Anda sudah kami tutup. Terima kasih telah menghubungi kami. Silakan chat untuk menghubungi kami kembali.',
            'status' => 'sent',
            'sent_at' => now(),
        ]);
        
        // Broadcast close message via WebSocket
        broadcast(new MessageSent(
            'Sesi Anda sudah kami tutup. Terima kasih telah menghubungi kami. Silakan chat untuk menghubungi kami kembali.',
            $sesi->id,
            $cs->id,
            $cs->name,
            'cs',
            null,
            null,
            now()->format('H:i')
        ))->toOthers();
        
        // Update status sesi
        $sesi->status = 'closed';
        $sesi->closed_at = now();
        $sesi->closed_by = $cs ? $cs->id : null;
        $sesi->last_message = 'Sesi ditutup';
        $sesi->last_activity = now();
        $sesi->save();
        
        // Broadcast session closed event to member
        broadcast(new SessionClosed(
            $sesi->id,
            'Sesi chat telah ditutup oleh Customer Service'
        ));
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Update typing status for CS
     * POST /cs/chat/{id}/typing
     */
    public function setTyping(Request $request, $id)
    {
        try {
            $cs = User::where('role', 'cs')->first();
            $sesi = Session::findOrFail($id);
            
            // Broadcast typing event via WebSocket
            broadcast(new UserTyping(
                $cs->id,
                $cs->name,
                'cs',
                $sesi->id
            ))->toOthers();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
    
    /**
     * Get messages list for polling (JSON response)
     * GET /cs/chat/{id}/messages
     */
    public function pesanList($id)
    {
        try {
            $sesi = Session::findOrFail($id);
            $cs = User::where('role', 'cs')->first();
            
            // REMOVED: Jangan auto-update status di polling
            // Status hanya update saat CS buka detail() pertama kali
            
            // Ambil semua Chat
            $pesans = Chat::where('session_id', $id)
                ->with(['senderMember', 'senderUser'])
                ->orderBy('sent_at', 'asc')
                ->get();
            
            $messages = $pesans->map(function($Chat) use ($cs) {
                $isCs = $Chat->sender_type === 'cs';
                return [
                    'text' => $Chat->message,
                    'file_path' => $Chat->file_path ? asset('storage/' . $Chat->file_path) : null,
                    'file_type' => $Chat->file_type,
                    'sender' => $Chat->sender ? $Chat->sender->name : 'Member',
                    'self' => $isCs,
                    'time' => \Carbon\Carbon::parse($Chat->sent_at)->format('H:i'),
                ];
            });
            
            return response()->json([
                'success' => true,
                'messages' => $messages,
                'session_id' => $sesi->id
            ]);
        } catch (\Exception $e) {
            \Log::error('CS pesanList error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'messages' => [],
                'member_typing' => false
            ], 500);
        }
    }

    /**
     * Handle session - Assign CS and send automatic greeting
     * POST /cs/chat/{id}/handle
     */
    public function handleSession($id)
    {
        try {
            // Only CS can handle sessions, NOT admin (view only)
            $cs = null;
            if (auth('cs')->check()) {
                $cs = auth('cs')->user();
            } elseif (auth('admin')->check()) {
                \Log::warning('Handle session blocked: Admin cannot handle sessions (view only)', [
                    'session_id' => $id,
                    'admin_id' => auth('admin')->id()
                ]);
                return response()->json(['success' => false, 'message' => 'Admin tidak dapat menangani sesi (view only)'], 403);
            } else {
                \Log::error('Handle session: No authenticated user');
                return response()->json(['success' => false, 'message' => 'User tidak terautentikasi'], 401);
            }
            
            if (!$cs) {
                \Log::error('Handle session: No authenticated user');
                return response()->json(['success' => false, 'message' => 'User tidak terautentikasi'], 401);
            }
            
            $sesi = Session::findOrFail($id);
            
            \Log::info('Handle session attempt', [
                'session_id' => $id,
                'cs_id' => $cs->id,
                'current_session_cs_id' => $sesi->cs_id,
                'current_status' => $sesi->status
            ]);
            
            if ($sesi->cs_id !== null) {
                return response()->json(['success' => false, 'message' => 'Sesi sudah ditangani'], 400);
            }
            
            $sesi->cs_id = $cs->id;
            $sesi->status = 'open';
            $sesi->save();
            
            $greetingMessage = "Terima kasih telah menghubungi kami, Chat Anda sedang kami proses oleh {$cs->name} (Customer Service)";
            
            Chat::create([
                'session_id' => $sesi->id,
                'sender_id' => $cs->id,
                'sender_type' => 'cs',
                'message' => $greetingMessage,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            
            $sesi->last_message = $greetingMessage;
            $sesi->last_activity = now();
            $sesi->save();
            
            \Log::info('Handle session success', ['session_id' => $id]);
            
            broadcast(new MessageSent($greetingMessage, $sesi->id, $cs->id, $cs->name, 'cs', null, null, now()->format('H:i')))->toOthers();
            
            return response()->json(['success' => true, 'message' => 'Sesi berhasil ditangani']);
        } catch (\Exception $e) {
            \Log::error('Handle session error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark messages as read when CS opens the chat
     * POST /cs/chat/{id}/mark-read
     */
    public function markRead($id)
    {
        // Detect current user role
        $currentUser = null;
        if (auth('cs')->check()) {
            $currentUser = auth('cs')->user();
        } elseif (auth('admin')->check()) {
            $currentUser = auth('admin')->user();
        } else {
            abort(401, 'Unauthorized');
        }
        
        // Only allow CS to mark as read (NOT admin - view only)
        if ($currentUser->role !== 'cs') {
            \Log::info('[Mark Read] Blocked for admin (view only)', ['session_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Admin is view-only, cannot mark messages as read'
            ], 403);
        }
        
        try {
            $sesi = Session::findOrFail($id);
            
            // Mark all member messages as read in this session
            $unreadMessages = Chat::where('session_id', $sesi->id)
                ->where('sender_type', 'member')
                ->where('is_read', false)
                ->get();
            
            \Log::info('[Mark Read] Found unread messages', [
                'session_id' => $sesi->id,
                'count' => $unreadMessages->count()
            ]);
            
            if ($unreadMessages->count() > 0) {
                $messageIds = $unreadMessages->pluck('id')->toArray();
                
                // Update both flags for consistency
                Chat::whereIn('id', $messageIds)->update(['is_read' => true, 'status' => 'read']);
                
                \Log::info('[Mark Read] Broadcasting read event', [
                    'session_id' => $sesi->id,
                    'message_ids' => $messageIds,
                    'channel' => 'chat.' . $sesi->id
                ]);
                
                // Broadcast read event to member
                broadcast(new MessagesRead($sesi->id, $messageIds))->toOthers();
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Messages marked as read',
                    'count' => count($messageIds),
                    'message_ids' => $messageIds
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'No unread messages', 'count' => 0]);
        } catch (\Exception $e) {
            \Log::error('Mark read error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error marking messages as read'], 500);
        }
    }
}
