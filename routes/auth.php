<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SesiChat;
use App\Http\Controllers\Cs\ChatController;
use App\Providers\RouteServiceProvider;


// Route untuk redirect sesuai role setelah login
Route::get('/redirect-by-role', function () {
    return redirect(RouteServiceProvider::redirectToByRole());
})->middleware('auth');

// Protected route (contoh dashboard)
Route::middleware(['auth:web','role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin/index');
    })->name('dashboard');

    Route::resource('/admin/members', UsersController::class)->only(['index']);
    Route::resource('/admin/sesi-chat', SesiChat::class)->only(['index']);
    Route::get('admin/sesi-chat/detail/{id}', [SesiChat::class, 'detail'])->name('admin.sesi-chat.detail');
});

Route::middleware(['auth:web','role:cs'])->group(function () {
    Route::get('/cs/dashboard', function () {
        return view('cs.index');
    })->name('cs.dashboard');

    // AJAX/live route dan view
    Route::get('cs/chat', [ChatController::class, 'index'])->name('cs.chat.index');
    Route::get('chat/{id}', [ChatController::class, 'detail'])->name('cs.chat.detail');
    Route::post('chat/{id}/send', [ChatController::class, 'sendMessage'])->name('cs.chat.send');
    Route::post('chat/{id}/close', [ChatController::class, 'closeSession'])->name('cs.chat.close');
});

Route::middleware(['auth:member','role:member'])->group(function () {
    Route::get('/dashboard', function () {
        return view('member.index');
    })->name('member.dashboard');
    
    Route::get('/room_chat', function () {
        return view('member.sections.room_chat_section');
    })->name('member.room_chat');
});
