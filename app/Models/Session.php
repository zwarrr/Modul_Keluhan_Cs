<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;
    
    protected $table = 'sessions';
    
    protected $fillable = [
        'member_id',
        'cs_id',
        'status',
        'rating_pelayanan',
        'rating_pelayanan_at',
        'last_message',
        'last_activity',
        'closed_by',
        'closed_at',
    ];

    protected $attributes = [
        'cs_id' => null,
    ];
    
    // Relasi: Member yang membuka sesi
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    // Relasi: CS yang menangani sesi
    public function cs()
    {
        return $this->belongsTo(User::class, 'cs_id');
    }

    // Relasi: Pesan-pesan dalam sesi ini
    public function chats()
    {
        return $this->hasMany(Chat::class, 'session_id');
    }

    // Relasi: Pesan terakhir dalam sesi (berdasarkan sent_at)
    public function lastChat()
    {
        // Use sent_at as primary ordering, and id as a deterministic tie-breaker
        // (important when multiple messages are created within the same second).
        return $this->hasOne(Chat::class, 'session_id')->ofMany([
            'sent_at' => 'max',
            'id' => 'max',
        ]);
    }
}
