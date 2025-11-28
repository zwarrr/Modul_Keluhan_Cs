<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat_sesi extends Model
{
    use HasFactory;
    protected $fillable = [
        'member_id',
        'cs_id',
        'status',
        'rating',
        'last_message',
        'last_activity',
        'closed_by',
        'closed_at',
    ];

    protected $attributes = [
        'cs_id' => null,
    ];
    // Relasi: Member (user) yang membuka sesi
    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    // Relasi: CS (user) yang menangani sesi
    public function cs()
    {
        return $this->belongsTo(User::class, 'cs_id');
    }

    // Relasi: Pesan-pesan dalam sesi ini
    public function pesan()
    {
        return $this->hasMany(Pesan::class, 'sesi_id');
    }
}
