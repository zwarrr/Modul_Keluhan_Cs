<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;

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
    return redirect('/login');
});

// rute untuk halaman login
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});



use App\Http\Controllers\Cs\ChatController;

// Route untuk halaman chat CS (list, detail, kirim pesan)
Route::prefix('cs')->group(function () {
    Route::get('chat', [ChatController::class, 'index'])->name('cs.chat.index');
    Route::get('chat/{id}', [ChatController::class, 'detail'])->name('cs.chat.detail');
    Route::post('chat/{id}/send', [ChatController::class, 'sendMessage'])->name('cs.chat.send');
    Route::post('chat/{id}/close', [ChatController::class, 'closeSession'])->name('cs.chat.close');
});


Require __DIR__.'/auth.php';