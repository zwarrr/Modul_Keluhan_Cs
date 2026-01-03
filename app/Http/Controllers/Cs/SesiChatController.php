<?php

namespace App\Http\Controllers\Cs;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Session;

class SesiChatController extends Controller
{
    /**
     * View-only detail of a chat session for CS.
     */
    public function detail($id)
    {
        $cs = auth('cs')->user();

        $sesi = Session::with(['member', 'cs'])->findOrFail($id);

        if ((int) $sesi->cs_id !== (int) $cs->id) {
            abort(403, 'Forbidden');
        }

        $pesans = Chat::where('session_id', $id)
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

        $pesanList = $pesans->map(function ($pesan) {
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

        return view('cs.sesi-chat.detail', [
            'id' => $id,
            'chatSesi' => $chatSesi,
            'pesans' => $pesanList,
        ]);
    }
}
