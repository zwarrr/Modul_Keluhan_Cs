<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        try {
            $userMessage = $request->input('message');
            
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
        }
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
