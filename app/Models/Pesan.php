<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    use HasFactory;
    protected $fillable = [
        'sesi_id',
        'member_id',
        'message',
        'status',
        'sent_at',
    ];
    // Relasi: Sesi chat dari pesan ini
    public function chatSesi()
    {
        return $this->belongsTo(Chat_sesi::class, 'chat_sesi_id');
    }

    // Relasi: User pengirim pesan
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: Parent pesan (jika reply)
    public function parent()
    {
        return $this->belongsTo(Pesan::class, 'parent_id');
    }

    // Relasi: Reply pesan (jika ada)
    public function replies()
    {
        return $this->hasMany(Pesan::class, 'parent_id');
    }
}
