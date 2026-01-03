<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SesiChat;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RatingController;
use App\Http\Controllers\Cs\ChatController;
use App\Http\Controllers\Cs\RatingController as CsRatingController;
use App\Http\Controllers\Cs\SesiChatController as CsSesiChatController;
use App\Providers\RouteServiceProvider;


// Route untuk redirect sesuai role setelah login
Route::get('/redirect-by-role', function () {
    return redirect(RouteServiceProvider::redirectToByRole());
})->middleware('auth');

// Protected route untuk Admin (menggunakan guard admin)
Route::middleware(['auth:admin','role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/admin/dataakuns', UsersController::class);
    Route::get('/admin/sesi-chat', [SesiChat::class, 'index'])->name('admin.sesi-chat.index');
    Route::get('admin/sesi-chat/detail/{id}', [SesiChat::class, 'detail'])->name('admin.sesi-chat.detail');
    Route::get('admin/sesi-chat/api/{id}', [SesiChat::class, 'apiDetail'])->name('admin.sesi-chat.api');
    Route::get('/admin/rating', [RatingController::class, 'index'])->name('admin.rating.index');
    
    // Admin bisa akses fitur CS (reuse CS controller, tapi tetap dengan guard admin)
    Route::get('admin/cs/chat', [ChatController::class, 'index'])->name('admin.cs.chat.index');
    Route::get('admin/cs/chat/{id}', [ChatController::class, 'detail'])->name('admin.cs.chat.detail');
    Route::get('admin/cs/chat/{id}/messages', [ChatController::class, 'pesanList'])->name('admin.cs.chat.pesanList');
    Route::post('admin/cs/chat/{id}/send', [ChatController::class, 'sendMessage'])->name('admin.cs.chat.send');
    Route::post('admin/cs/chat/{id}/typing', [ChatController::class, 'setTyping'])->name('admin.cs.chat.typing');
    Route::post('admin/cs/chat/{id}/close', [ChatController::class, 'closeSession'])->name('admin.cs.chat.close');
    Route::post('admin/cs/chat/{id}/handle', [ChatController::class, 'handleSession'])->name('admin.cs.chat.handle');
    Route::post('admin/cs/chat/{id}/mark-read', [ChatController::class, 'markRead'])->name('admin.cs.chat.markRead');
});

// Protected route untuk CS (menggunakan guard cs)
Route::middleware(['auth:cs','role:cs'])->group(function () {
    Route::get('/cs/dashboard', function () {
        return view('cs.index');
    })->name('cs.dashboard');
});

// CS Chat routes - accessible by CS only
Route::middleware(['auth:cs','role:cs'])->group(function () {
    Route::get('cs/chat', [ChatController::class, 'index'])->name('cs.chat.index');
    Route::get('cs/chat/{id}', [ChatController::class, 'detail'])->name('cs.chat.detail');
    Route::get('cs/chat/{id}/messages', [ChatController::class, 'pesanList'])->name('cs.chat.pesanList');
    Route::post('cs/chat/{id}/send', [ChatController::class, 'sendMessage'])->name('cs.chat.send');
    Route::post('cs/chat/{id}/typing', [ChatController::class, 'setTyping'])->name('cs.chat.typing');
    Route::post('cs/chat/{id}/close', [ChatController::class, 'closeSession'])->name('cs.chat.close');
    Route::post('cs/chat/{id}/handle', [ChatController::class, 'handleSession'])->name('cs.chat.handle');
    Route::post('cs/chat/{id}/mark-read', [ChatController::class, 'markRead'])->name('cs.chat.markRead');
    
    // CS Profile routes
    Route::get('cs/profile', [\App\Http\Controllers\Cs\ProfileController::class, 'index'])->name('cs.profile.index');
    Route::put('cs/profile', [\App\Http\Controllers\Cs\ProfileController::class, 'update'])->name('cs.profile.update');
    Route::put('cs/profile/password', [\App\Http\Controllers\Cs\ProfileController::class, 'updatePassword'])->name('cs.profile.updatePassword');

    // CS rating page (Ratting Saya)
    Route::get('cs/rating', [CsRatingController::class, 'index'])->name('cs.rating.index');

    // CS view-only session detail (same layout as admin detail)
    Route::get('cs/sesi-chat/detail/{id}', [CsSesiChatController::class, 'detail'])->name('cs.sesi-chat.detail');
});

// Logout untuk CS dan Admin (satu route, detect guard otomatis)
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:member','role:member'])->group(function () {
    Route::get('/dashboard', function () {
        return view('member.index');
    })->name('member.dashboard');
    
    Route::get('/room_chat', function () {
        return view('member.sections.room_chat_section');
    })->name('member.room_chat');
});
