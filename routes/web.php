<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// otomatis redirect 
Route::get('/', function () {
    return view('welcome');
});

// route untuk room chat (harus login sebagai member)
Route::middleware(['auth:member', 'role:member'])->group(function () {
    Route::get('/member/chatroom', function () {
        return view('member.sections.room_chat_section');
    })->name('chatroom');
    
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// rute untuk halaman login admin/cs
Route::middleware(['guest:web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});


// rute untuk halaman login member
Route::middleware(['guest:member'])->group(function () {
    Route::get('/member/login', [AuthController::class, 'showMemberLoginForm'])->name('member.login.form');
    Route::post('/member/login', [AuthController::class, 'memberLogin'])->name('member.login');
});

Require __DIR__.'/auth.php';