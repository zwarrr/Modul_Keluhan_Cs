<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    
    protected $table = 'chats';
    
    protected $fillable = [
        'session_id',
        'sender_id',
        'sender_type',
        'message',
        'file_path',
        'file_type',
        'status',
        'sent_at',
    ];
    
    // Relasi: Sesi chat dari pesan ini
    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    // Relasi: Pengirim pesan - Member
    public function senderMember()
    {
        return $this->belongsTo(Member::class, 'sender_id');
    }

    // Relasi: Pengirim pesan - User (CS/Admin)
    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Accessor untuk mendapatkan nama sender
    public function getSenderAttribute()
    {
        if ($this->isSystemGreetingMessage()) {
            return (object) ['name' => 'System Adm'];
        }

        if ($this->sender_type === 'member') {
            return $this->senderMember;
        } else {
            return $this->senderUser;
        }
    }

    private function isSystemGreetingMessage(): bool
    {
        if ($this->sender_type !== 'cs') {
            return false;
        }

        $message = (string) ($this->message ?? '');
        $messageTrimmed = ltrim($message);

        // Greeting created when member starts a new session
        if (str_starts_with($messageTrimmed, 'Halo ') && str_contains($message, 'Terimakasih telah menghubungi layanan kami')) {
            return true;
        }

        return false;
    }
}
