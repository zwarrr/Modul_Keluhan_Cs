<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            // redirect untuk 3 role
            if (auth()->user()->role === 'admin') {
                return redirect()->intended('admin/dashboard');
            } elseif (auth()->user()->role === 'cs') {
                return redirect()->intended('cs/dashboard');
            } else {
                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }
}
