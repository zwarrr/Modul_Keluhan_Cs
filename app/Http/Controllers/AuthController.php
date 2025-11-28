<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Cek jika sudah login, redirect sesuai role
        if (auth()->check()) {
            if (auth()->user()->role === 'admin') {
                return redirect('admin/dashboard');
            } elseif (auth()->user()->role === 'cs') {
                return redirect('cs/dashboard');
            } else {
                return redirect('/dashboard');
            }
        }
        
        return view('auth.login');
    }

    public function showMemberLoginForm()
    {
        // Cek jika sudah login sebagai member, redirect ke chatroom
        if (auth('member')->check()) {
            return redirect('/member/chatroom');
        }
        
        return view('auth.login_member');
    }

    public function memberLogin(Request $request)
    {
        // validasi input untuk member (menggunakan member_id)
        $request->validate([
            'member_id' => 'required|string',
            'password' => 'required|string',
        ]);
        
        // Cari user berdasarkan member_id dan role member
        $user = \App\Models\User::where('member_id', $request->member_id)
                                ->where('role', 'member')
                                ->first();
        
        if ($user && \Hash::check($request->password, $user->password)) {
            auth('member')->login($user, $request->filled('remember'));
            
            // Return JSON response for AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => [
                        'title' => 'Login Berhasil!',
                        'subtitle' => 'Selamat datang kembali, ' . $user->member_id,
                        'text' => ''
                    ],
                    'redirect' => route('chatroom')
                ]);
            }
            
            // Redirect ke chatroom setelah login sukses (fallback)
            return redirect()->intended('/member/chatroom');
        }

        // Return JSON response for failed login
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'ID Member atau password yang Anda masukkan salah.'
            ], 401);
        }

        return back()->withErrors([
            'member_id' => 'ID atau password salah.',
        ])->withInput($request->only('member_id'));
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

    public function logout(Request $request)
    {
        auth('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/member/login');
    }
}
