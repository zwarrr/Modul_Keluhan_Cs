<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'member_id',
        'name',
        'email',
        'password',
        'address',
        'phone_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi: Sesi chat yang dibuka member
    public function sessions()
    {
        return $this->hasMany(Session::class, 'member_id');
    }

    // Relasi: Pesan yang dikirim member
    public function chats()
    {
        return $this->hasMany(Chat::class, 'sender_id')
            ->where('sender_type', 'member');
    }
}
