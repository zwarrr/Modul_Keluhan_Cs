<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Members\ChatController as MemberChatController;

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
// Logout routes - separated by role
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::match(['get', 'post'], '/member/logout', [AuthController::class, 'memberLogout'])->name('member.logout');


// otomatis redirect 
Route::get('/', function () {
    return redirect('/member/login');
});

// route untuk room chat (harus login sebagai member)
Route::middleware(['auth:member', 'role:member'])->group(function () {
    Route::get('/member/chat', [MemberChatController::class, 'chatList'])->name('chat.list');
    Route::get('/member/chat/sessions', [MemberChatController::class, 'sessionsJson'])->name('chat.sessions');
    
    Route::get('/member/chatroom', function () {
        return view('member.sections.room_chat_section');
    })->name('chatroom');
    
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/typing', [ChatController::class, 'setMemberTyping'])->name('chat.typing');
    Route::post('/chat/new-session', [ChatController::class, 'createNewSession'])->name('chat.newSession');
    Route::post('/chat/end-session', [ChatController::class, 'endSession'])->name('chat.endSession');
    Route::post('/chat/rating-pelayanan', [ChatController::class, 'ratePelayanan'])->name('chat.ratingPelayanan');
    Route::get('/api/member/session/{id}', [ChatController::class, 'getSessionDetails'])->name('chat.sessionDetails');
});

// rute untuk halaman login admin/cs (satu URL, guard berbeda)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// rute untuk halaman login member
Route::middleware(['guest:member'])->group(function () {
    Route::get('/member/login', [AuthController::class, 'showMemberLoginForm'])->name('member.login.form');
    Route::post('/member/login', [AuthController::class, 'memberLogin'])->name('member.login');
});

Require __DIR__.'/auth.php';