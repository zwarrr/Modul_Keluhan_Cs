<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UsersController;

use App\Providers\RouteServiceProvider;
// Route untuk redirect sesuai role setelah login
Route::get('/redirect-by-role', function () {
    return redirect(RouteServiceProvider::redirectToByRole());
})->middleware('auth');

// Protected route (contoh dashboard)
Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin/index');
    })->name('dashboard');
    
    // untuk CRUD cs oleh admin
    Route::get('/admin/costumer-services', [UsersController::class, 'cs'])->name('admin.cs.index');

    // untuk CRUD member oleh admin
    Route::get('/admin/members', [UsersController::class, 'member'])->name('admin.member.index');
});

Route::middleware(['auth','role:cs'])->group(function () {
    Route::get('/cs/dashboard', function () {
        return view('cs.index');
    })->name('cs.dashboard');
});

Route::middleware(['auth','role:member'])->group(function () {
    Route::get('/dashboard', function () {
        return view('member.index');
    })->name('member.dashboard');
});
