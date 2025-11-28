<?php

namespace App\Http\Controllers\Cs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat_sesi;
use App\Models\Pesan;
use App\Models\User;

class ChatController extends Controller
{
        /**
         * CS mengirim pesan ke sesi tertentu
         * POST /cs/chat/{id}/send
         * Body: { message }
         */
        public function sendMessage(Request $request, $id)
        {
            $request->validate([
                'message' => 'required|string',
            ]);

            $cs = User::where('role', 'cs')->first();
            $sesi = Chat_sesi::findOrFail($id);

            // Jika sesi belum ada CS, update cs_id
            if (!$sesi->cs_id) {
                $sesi->cs_id = $cs->id;
            }
            $sesi->last_message = $request->message;
            $sesi->last_activity = now();
            $sesi->save();

            // Buat pesan baru dari CS
            $pesan = Pesan::create([
                'sesi_id' => $sesi->id,
                'member_id' => $cs->id, // kolom member_id dipakai untuk user pengirim (CS)
                'message' => $request->message,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'pesan_id' => $pesan->id,
            ]);
        }
    public function index(Request $request)
    {
        // Ambil CS login, dummy: ambil CS pertama
        $cs = User::where('role', 'cs')->first();

        // Query chat session yang ditangani CS ini atau belum ada CS (cs_id null)
        $query = Chat_sesi::with(['member', 'pesan' => function($q){ $q->latest('sent_at'); }])
            ->where(function($q) use ($cs) {
                $q->where('cs_id', $cs->id)
                  ->orWhereNull('cs_id');
            });

        // Filter unread/read
        if ($request->read === 'unread') {
            $query->whereHas('pesan', function($q) use ($cs) {
                $q->where('member_id', '!=', $cs->id)->where('status', 'sent');
            });
        } elseif ($request->read === 'read') {
            $query->whereDoesntHave('pesan', function($q) use ($cs) {
                $q->where('member_id', '!=', $cs->id)->where('status', 'sent');
            });
        }

        // Filter search
        if ($request->search) {
            $query->whereHas('member', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            });
        }

        // Sorting
        $sort = $request->sort === 'asc' ? 'asc' : 'desc';
        $query->orderBy('last_activity', $sort);

        $sessions = $query->get();

        // Format data untuk list
        $chatList = $sessions->map(function($sesi) use ($cs) {
            $lastPesan = $sesi->pesan->sortByDesc('sent_at')->first();
            // Hanya hitung pesan member yang statusnya 'sent'
            $unread = $sesi->pesan
                ->filter(function($p) {
                    return $p->user && $p->user->role === 'member' && $p->status === 'sent';
                })->count();
            // Jika ada pesan member baru, unread harus > 0
            // Untuk debug, tambahkan log jika perlu
            return [
                'id' => $sesi->id,
                'name' => $sesi->member ? $sesi->member->name : '-',
                'avatar' => $sesi->member ? 'https://ui-avatars.com/api/?name='.urlencode($sesi->member->name) : '',
                'last_message' => $lastPesan ? $lastPesan->message : '-',
                'last_time' => $lastPesan && $lastPesan->sent_at ? (\Carbon\Carbon::parse($lastPesan->sent_at)->format('H:i')) : '',
                'unread' => $unread,
                'status' => $sesi->status,
            ];
        });

        if ($request->ajax()) {
            return response()->json(['data' => $chatList]);
        }

        return view('cs.chat.index', compact('chatList'));
    }
    public function detail($id)
    {
        // Ambil data sesi dan pesan berdasarkan id
        $sesi = \App\Models\Chat_sesi::with('member')->findOrFail($id);
        // Update status pesan member menjadi 'read' jika masih 'sent'
        \App\Models\Pesan::where('sesi_id', $id)
            ->whereHas('user', function($q){ $q->where('role', 'member'); })
            ->where('status', 'sent')
            ->update(['status' => 'read']);
        $pesans = \App\Models\Pesan::where('sesi_id', $id)->orderBy('sent_at')->get();

        // Format untuk view/JSON
        $chatSesi = [
            'id' => $sesi->id,
            'member' => $sesi->member ? $sesi->member->name : '-',
            'avatar' => $sesi->member ? 'https://ui-avatars.com/api/?name='.urlencode($sesi->member->name) : '',
            'status' => $sesi->status,
        ];
        $pesanList = $pesans->map(function($pesan) {
            return [
                'sender' => $pesan->user ? $pesan->user->name : '-',
                'role' => $pesan->user && $pesan->user->role ? $pesan->user->role : 'member',
                'message' => $pesan->message,
                'time' => $pesan->sent_at ? (\Carbon\Carbon::parse($pesan->sent_at)->format('H:i')) : '',
            ];
        })->values();

        // Jika AJAX polling, return partial HTML pesan saja
        if (request('ajax')) {
            $html = view('cs.chat._messages', ['pesans' => $pesanList])->render();
            return response($html);
        }

        return view('cs.chat.detail', [
            'chatSesi' => $chatSesi,
            'pesans' => $pesanList,
        ]);
    }

        /**
     * Tutup sesi chat (update status jadi closed)
     * POST /cs/chat/{id}/close
     */
    public function closeSession(Request $request, $id)
    {
        $sesi = \App\Models\Chat_sesi::findOrFail($id);
        // Ambil CS yang sedang login (sementara: CS pertama)
        $cs = \App\Models\User::where('role', 'cs')->first();
        $sesi->status = 'closed';
        $sesi->closed_at = now();
        $sesi->closed_by = $cs ? $cs->id : null;
        $sesi->save();
        return response()->json(['success' => true]);
    }
}