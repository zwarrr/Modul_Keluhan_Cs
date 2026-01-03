<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Events\UserTyping;
use App\Events\MessageSent;
use App\Events\SessionClosed;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:2000',
            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,bmp,pdf,doc,docx,xls,xlsx,txt,zip,rar',
        ]);

        try {
            $userMessage = $request->input('message');
            
            // Ambil member yang sedang login
            $member = auth('member')->user();
            
            if (!$member) {
                \Log::error('Member not authenticated', [
                    'session' => session()->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Silakan login terlebih dahulu.'
                ], 401);
            }
            
            // Cari chat session yang masih open/pending
            $sesi = \App\Models\Session::where('member_id', $member->id)
                ->whereIn('status', ['open', 'pending'])
                ->orderBy('created_at', 'desc')
                ->first();
            
            $isNewSession = false;
            // Jika belum ada sesi, buat baru
            if (!$sesi) {
                $sesi = \App\Models\Session::create([
                    'member_id' => $member->id,
                    'status' => 'pending',
                    'last_message' => $userMessage ?: '[File]',
                    'last_activity' => now(),
                ]);
                $isNewSession = true;
            } else {
                // Update sesi yang sudah ada
                $sesi->last_message = $userMessage ?: '[File]';
                $sesi->last_activity = now();
                $sesi->save();
            }
            
            // Handle file upload
            $filePath = null;
            $fileType = null;
            
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                
                // Tentukan tipe file
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                $fileType = in_array(strtolower($extension), $imageExtensions) ? 'image' : 'file';
                
                // Buat path berdasarkan member_id dan tipe file
                $memberIdFolder = $member->member_id;
                $typeFolder = $fileType === 'image' ? 'img' : 'file';
                $storagePath = "chat_files/{$memberIdFolder}/{$typeFolder}";
                
                // Hitung jumlah file yang sudah ada untuk member ini
                $existingFilesCount = \App\Models\Chat::where('session_id', $sesi->id)
                    ->where('file_type', $fileType)
                    ->whereNotNull('file_path')
                    ->count();
                $fileNumber = $existingFilesCount + 1;
                
                // Format nama file: camera_202020_12-10-2025_1.jpg atau file_202020_12-10-2025_1.pdf
                $date = now()->format('d-m-Y');
                $prefix = $fileType === 'image' ? 'camera' : 'file';
                $fileName = "{$prefix}_{$memberIdFolder}_{$date}_{$fileNumber}.{$extension}";
                
                // Simpan file ke storage/app/public/chat_files/{member_id}/img atau file
                $filePath = $file->storeAs($storagePath, $fileName, 'public');
            }
            
            // Simpan pesan member ke database
            $pesan = \App\Models\Chat::create([
                'session_id' => $sesi->id,
                'sender_id' => $member->id,
                'sender_type' => 'member',
                'message' => $userMessage,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            
            // Kirim greeting SEBELUM broadcast pesan member (jika sesi baru)
            if ($isNewSession) {
                $cs = \App\Models\User::where('role', 'cs')->first();
                if ($cs) {
                    $greetingMessage = "Halo {$member->name}! 👋\n\nTerimakasih telah menghubungi layanan kami. Ada yang bisa kami bantu hari ini?😊";
                    
                    $greetingChat = \App\Models\Chat::create([
                        'session_id' => $sesi->id,
                        'sender_id' => $cs->id,
                        'sender_type' => 'cs',
                        'message' => $greetingMessage,
                        'status' => 'read',
                        'is_read' => true,
                        'sent_at' => now(),
                    ]);

                    // Pastikan preview list chat mengikuti pesan terakhir (greeting)
                    $sesi->last_message = $greetingMessage;
                    $sesi->last_activity = now();
                    $sesi->save();
                    
                    // Broadcast greeting via WebSocket
                    broadcast(new MessageSent(
                        $greetingMessage,
                        $sesi->id,
                        $cs->id,
                        'System Adm',
                        'cs',
                        null,
                        null,
                        now()->format('H:i'),
                        'read',
                        $greetingChat->id
                    ));
                }
            }
            
            // Broadcast pesan member via WebSocket
            $fileUrl = $filePath ? asset('storage/' . $filePath) : null;
            broadcast(new MessageSent(
                $userMessage,
                $sesi->id,
                $member->id,
                $member->name,
                'member',
                $fileUrl,
                $fileType,
                now()->format('H:i'),
                'sent',
                $pesan->id
            ));
            
            return response()->json([
                'success' => true,
                'message' => 'Pesan terkirim, mohon tunggu balasan dari CS.',
                'timestamp' => now()->format('H:i')
            ]);
            
            /* DEEPSEEK API DINONAKTIFKAN SEMENTARA
            // Get current datetime in configured timezone
            $now = now()->timezone(config('app.timezone'));
            $currentTime = $now->format('H:i');
            $currentDay = $now->translatedFormat('l'); // Senin, Selasa, dst
            $currentDate = $now->translatedFormat('d F Y');
            
            // System prompt untuk AI
            $systemPrompt = "
Kamu adalah asisten virtual customer service yang ramah dan helpful.

GAYA KOMUNIKASI:
✓ Ramah dan natural
✓ Ringkas dan langsung ke poin
✓ Tidak bertele-tele

FORMAT JAWABAN:
• Gunakan paragraf pendek (2-3 baris)
• Pisahkan poin dengan baris kosong
• Gunakan bullet ✓ atau numbering 1. 2. 3. untuk list
• Emoji minimal, hanya jika sangat relevan
• Jangan gunakan markdown (**, *, #, ```)

ATURAN WAKTU:
Jika ditanya jam:
\"Sekarang jam {$currentTime}\"

Jika ditanya detail waktu/tanggal lengkap:
\"Sekarang jam {$currentTime}, hari {$currentDay}, tanggal {$currentDate}\"

Jika ditanya hari:
\"Sekarang hari {$currentDay}\"

Jika ditanya tanggal/bulan/tahun:
\"Sekarang tanggal {$currentDate}\"

ATURAN DETAIL:
• Jawaban standar: ringkas 2-3 kalimat
• Jika diminta detail (\"jelasin detail\", \"lebih lengkap\"), baru kasih penjelasan lengkap

CONTOH JAWABAN:

User: \"Jam berapa sekarang?\"
Jawab: \"Sekarang jam {$currentTime}\"

User: \"Sekarang hari apa tanggal berapa?\"
Jawab: \"Sekarang jam {$currentTime}, hari {$currentDay}, tanggal {$currentDate}\"

User: \"Apa itu Laravel?\"
Jawab: \"Laravel itu framework PHP yang bikin coding web jadi lebih mudah dan rapi.

Keunggulannya:
✓ Struktur kode terorganisir
✓ Fitur lengkap (database, login, routing)
✓ Komunitas besar dan dokumentasi jelas

Cocok buat bikin website dari yang simple sampai kompleks.\"

User: \"Halo\"
Jawab: \"Halo! Ada yang bisa saya bantu?\"

INGAT: Jawab sesuai pertanyaan, jangan kasih info lebih dari yang ditanya. Kalau ditanya jam ya jawab jam aja, kalau ditanya detail baru kasih lengkap!
";
            

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'Content-Type' => 'application/json',
                'HTTP-Referer' => env('APP_URL'),
                'X-Title' => env('APP_NAME'),
            ])
            ->withOptions([
                'verify' => false, // Disable SSL verification for development
            ])
            ->timeout(60)->post(env('OPENROUTER_API_URL'), [
                'model' => env('OPENROUTER_MODEL'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiResponse = $data['choices'][0]['message']['content'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda saat ini.';
                
                // Hapus markdown formatting
                $aiResponse = $this->removeMarkdown($aiResponse);
                
                return response()->json([
                    'success' => true,
                    'message' => $aiResponse,
                    'timestamp' => now()->format('H:i')
                ]);
            } else {
                Log::error('OpenRouter API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, terjadi kesalahan. Silakan coba lagi.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Chat Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Maaf, sistem sedang mengalami gangguan. Silakan coba beberapa saat lagi.',
            ], 500);
            END DEEPSEEK API COMMENT */
        } catch (\Exception $e) {
            \Log::error('Chat Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan. Silakan coba lagi.',
            ], 500);
        }
    }
    
    /**
     * Get messages for member chat (polling endpoint)
     */
    public function getMessages(Request $request)
    {
        try {
            $member = auth('member')->user();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'messages' => [],
                    'message' => 'Unauthorized'
                ], 401);
            }

            $offset = max(0, (int) $request->query('sessions_offset', 0));
            $limit = (int) $request->query('sessions_limit', 1);
            if ($limit < 1) {
                $limit = 1;
            }
            if ($limit > 10) {
                $limit = 10;
            }

            $sessionsQuery = \App\Models\Session::where('member_id', $member->id)
                ->orderBy('created_at', 'desc');

            $totalSessions = (clone $sessionsQuery)->count();
            $latestSession = (clone $sessionsQuery)->first();

            // Active session used for websocket subscription
            // Member always subscribes to latest session (even if closed) 
            // because CS can reopen it by sending a message
            $activeSesi = $latestSession;

            // Build session index (Sesi 1..N) in chronological order
            $sessionIdAsc = \App\Models\Session::where('member_id', $member->id)
                ->orderBy('created_at', 'asc')
                ->pluck('id')
                ->toArray();
            $sessionIndexMap = [];
            $i = 1;
            foreach ($sessionIdAsc as $sid) {
                $sessionIndexMap[(string) $sid] = $i;
                $i++;
            }

            $sessionsSlice = (clone $sessionsQuery)
                ->skip($offset)
                ->take($limit)
                ->get();

            // If newest session has no messages yet, include previous session so chat isn't blank
            if ($offset === 0 && $limit === 1 && $sessionsSlice->count() === 1 && $totalSessions > 1) {
                $newest = $sessionsSlice->first();
                $newestHasMessages = $newest
                    ? \App\Models\Chat::where('session_id', $newest->id)->exists()
                    : false;
                if (!$newestHasMessages) {
                    $extra = (clone $sessionsQuery)->skip(1)->take(1)->get();
                    $sessionsSlice = $sessionsSlice->concat($extra);
                    $limit = 2;
                }
            }

            // Return sessions in chronological order within the slice
            $sessionsChrono = $sessionsSlice->reverse()->values();

            $items = [];
            foreach ($sessionsChrono as $session) {
                $pesans = \App\Models\Chat::where('session_id', $session->id)
                    ->with(['senderMember', 'senderUser'])
                    ->orderBy('sent_at', 'asc')
                    ->get();

                // Session start marker (per-session badge)
                if ($session->status === 'closed') {
                    $idx = $sessionIndexMap[(string) $session->id] ?? null;
                    $badgeDate = $session->closed_at ?: ($session->last_activity ?: $session->created_at);
                    $labelDate = $badgeDate ? \Carbon\Carbon::parse($badgeDate)->format('d-m-Y') : '';
                    $items[] = [
                        'type' => 'session_badge',
                        'session_id' => $session->id,
                        'label' => 'Sesi ' . ($idx ?? '-') . ($labelDate ? ' | ' . $labelDate : ''),
                    ];
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

                    $items[] = [
                        'type' => 'date_badge',
                        'session_id' => $session->id,
                        'label' => $label,
                    ];
                }

                // Mark CS messages as read only for the active session
                if ($activeSesi && (int) $activeSesi->id === (int) $session->id) {
                    \App\Models\Chat::where('session_id', $session->id)
                        ->where('sender_type', 'cs')
                        ->where('status', 'sent')
                        ->update(['status' => 'read', 'is_read' => true]);
                }

                foreach ($pesans as $pesan) {
                    $isSelf = $pesan->sender_type === 'member' && (int) $pesan->sender_id === (int) $member->id;

                    $items[] = [
                        'type' => 'message',
                        'id' => $pesan->id,
                        'text' => $pesan->message,
                        'file_path' => $pesan->file_path ? asset('storage/' . $pesan->file_path) : null,
                        'file_type' => $pesan->file_type,
                        'self' => $isSelf,
                        'time' => \Carbon\Carbon::parse($pesan->sent_at)->format('H:i'),
                        'date' => \Carbon\Carbon::parse($pesan->sent_at)->format('Y-m-d'),
                        'status' => $pesan->status,
                        'sent_at' => \Carbon\Carbon::parse($pesan->sent_at)->toIso8601String(),
                        'session_id' => $session->id,
                        'session_closed' => ($session->status === 'closed'),
                    ];
                }
            }

            $hasMoreSessions = ($offset + $limit) < $totalSessions;
            $sessionClosed = (!$activeSesi && $latestSession && $latestSession->status === 'closed');

            return response()->json([
                'success' => true,
                'messages' => $items,
                'active_session_id' => $activeSesi ? $activeSesi->id : null,
                'cs_typing' => false,
                'session_closed' => $sessionClosed,
                'loaded_sessions' => $sessionsChrono->pluck('id')->values(),
                'sessions_offset' => $offset,
                'sessions_limit' => $limit,
                'next_sessions_offset' => $offset + $limit,
                'has_more_sessions' => $hasMoreSessions,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get Messages Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'messages' => []
            ], 500);
        }
    }
    
    /**
     * Update typing status for member (called by member when typing)
     */
    public function setMemberTyping(Request $request)
    {
        try {
            $member = auth('member')->user();
            
            $sesi = \App\Models\Session::where('member_id', $member->id)
                ->whereIn('status', ['open', 'pending'])
                ->first();
            
            if ($sesi) {
                // Broadcast typing event via WebSocket
                broadcast(new UserTyping(
                    $member->id,
                    $member->name,
                    'member',
                    $sesi->id
                ))->toOthers();
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
    
    /**
     * Create new session for member (called when member clicks "Chat Baru")
     * JANGAN BUAT SESI - Biarkan otomatis pas kirim pesan pertama
     */
    public function createNewSession(Request $request)
    {
        try {
            $member = auth('member')->user();
            
            \Log::info('Chat Baru clicked by member:', ['member_id' => $member->id]);

            // If there's already an active session, just return it
            $activeSesi = \App\Models\Session::where('member_id', $member->id)
                ->whereIn('status', ['open', 'pending'])
                ->orderBy('created_at', 'desc')
                ->first();
            if ($activeSesi) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sesi masih aktif.',
                    'session_id' => $activeSesi->id,
                ]);
            }

            // Create a new session record (history stays; still one room)
            $newSesi = \App\Models\Session::create([
                'member_id' => $member->id,
                'status' => 'pending',
                'last_message' => null,
                'last_activity' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesi baru dibuat.',
                'session_id' => $newSesi->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in createNewSession:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * End current active session for member
     * POST /chat/end-session
     */
    public function endSession(Request $request)
    {
        try {
            $member = auth('member')->user();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Silakan login terlebih dahulu.'
                ], 401);
            }

            $sesi = \App\Models\Session::where('member_id', $member->id)
                ->whereIn('status', ['open', 'pending'])
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$sesi) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada sesi aktif.'
                ]);
            }

            // Cek apakah sesi sudah ditangani oleh CS
            if (!$sesi->cs_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi belum ditangani oleh Customer Service. Anda tidak dapat mengakhiri sesi ini.'
                ], 400);
            }

            // Kirim pesan penutup dari CS
            $cs = \App\Models\User::find($sesi->cs_id);
            $csName = $cs ? $cs->name : 'Customer Service';
            
            $closeMessage = \App\Models\Chat::create([
                'session_id' => $sesi->id,
                'sender_id' => $sesi->cs_id,
                'sender_type' => 'cs',
                'message' => 'Sesi sudah anda tutup. Terima kasih telah menghubungi kami. Silahkan chat untuk menghubungi kami kembali.',
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            
            // Broadcast close message via WebSocket
            broadcast(new \App\Events\MessageSent(
                'Sesi sudah anda tutup. Terima kasih telah menghubungi kami. Silahkan chat untuk menghubungi kami kembali.',
                $sesi->id,
                $sesi->cs_id,
                $csName,
                'cs',
                null,
                null,
                now()->format('H:i'),
                $closeMessage->id
            ))->toOthers();

            $sesi->status = 'closed';
            $sesi->closed_at = now();
            $sesi->closed_by = 'member';
            $sesi->last_message = 'Member telah menutup sesi chat ini';
            $sesi->last_activity = now();
            $sesi->save();

            broadcast(new SessionClosed(
                $sesi->id,
                'Sesi chat telah diakhiri oleh Member'
            ));

            return response()->json([
                'success' => true,
                'sesi_id' => $sesi->id
            ]);
        } catch (\Exception $e) {
            \Log::error('End Session Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Save member rating for a closed session
     * POST /chat/rating-pelayanan
     */
    public function ratePelayanan(Request $request)
    {
        $member = auth('member')->user();
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $validated = $request->validate([
                'session_id' => ['required', 'integer'],
                'rating_pelayanan' => ['required', 'integer', 'min:1', 'max:5'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors(),
            ], 422);
        }

        $sesi = \App\Models\Session::where('id', (int) $validated['session_id'])
            ->where('member_id', (int) $member->id)
            ->first();

        if (!$sesi) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan'
            ], 404);
        }

        if ($sesi->status !== 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi belum berakhir'
            ], 400);
        }

        if ($sesi->rating_pelayanan !== null) {
            return response()->json([
                'success' => true,
                'message' => 'Rating sudah tersimpan'
            ]);
        }

        $sesi->rating_pelayanan = (int) $validated['rating_pelayanan'];
        $sesi->rating_pelayanan_at = now();
        $sesi->save();

        return response()->json([
            'success' => true,
            'message' => 'Rating tersimpan'
        ]);
    }

    /**
     * Get session details for member
     * GET /api/member/session/{id}
     */
    public function getSessionDetails($id)
    {
        $member = auth('member')->user();
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $sesi = \App\Models\Session::where('id', $id)
            ->where('member_id', $member->id)
            ->first();

        if (!$sesi) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'id' => $sesi->id,
            'cs_id' => $sesi->cs_id,
            'status' => $sesi->status,
            'member_id' => $sesi->member_id,
        ]);
    }

    private function removeMarkdown($text)
    {
        // Hapus code blocks
        $text = preg_replace('/```[\s\S]*?```/', '', $text);
        $text = preg_replace('/`([^`]+)`/', '$1', $text);
        
        // Hapus bold
        $text = preg_replace('/\*\*([^*]+)\*\*/', '$1', $text);
        $text = preg_replace('/__([^_]+)__/', '$1', $text);
        
        // Hapus italic
        $text = preg_replace('/\*([^*]+)\*/', '$1', $text);
        $text = preg_replace('/_([^_]+)_/', '$1', $text);
        
        // Hapus headers
        $text = preg_replace('/^#{1,6}\s+/m', '', $text);
        
        // Hapus links [text](url)
        $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text);
        
        // Hapus bullet points
        $text = preg_replace('/^[\*\-\+]\s+/m', '', $text);
        
        // Hapus numbered lists
        $text = preg_replace('/^\d+\.\s+/m', '', $text);
        
        // Bersihkan whitespace berlebih
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        $text = trim($text);
        
        return $text;
    }
}



