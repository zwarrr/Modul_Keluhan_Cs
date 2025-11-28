<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Endpoint API untuk member kirim pesan (buat sesi & pesan sekaligus)
Route::post('/member/chat/send', [\App\Http\Controllers\Members\ChatController::class, 'sendMessage']);

// Endpoint API untuk get data sesi (detail per sesi, ada member & cs)
Route::get('/member/chat/sesi/{id}', [\App\Http\Controllers\Members\ChatController::class, 'getSesi']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
