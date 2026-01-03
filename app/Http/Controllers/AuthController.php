<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Tampilkan form login untuk CS dan Admin
        return view('auth.login');
    }

    public function showMemberLoginForm()
    {
        // Cek jika sudah login sebagai member, redirect ke chat list
        if (auth('member')->check()) {
            return redirect('/member/chat');
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
        
        // Cari member berdasarkan member_id
        $member = \App\Models\Member::where('member_id', $request->member_id)
                                ->first();
        
        if ($member && \Hash::check($request->password, $member->password)) {
            auth('member')->login($member, $request->filled('remember'));
            
            // Return JSON response for AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => [
                        'title' => 'Login Berhasil!',
                        'subtitle' => 'Selamat datang kembali, ' . $member->member_id,
                        'text' => ''
                    ],
                    'redirect' => route('chat.list')
                ]);
            }
            
            // Redirect ke chat list setelah login sukses (fallback)
            return redirect()->intended('/member/chat');
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
        
        // Cari user berdasarkan email
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user && \Hash::check($request->password, $user->password)) {
            $request->session()->forget('url.intended');
            
            // Login dengan guard sesuai role
            if ($user->role === 'admin') {
                auth('admin')->login($user, $request->filled('remember'));
                return redirect('admin/dashboard');
            } elseif ($user->role === 'cs') {
                auth('cs')->login($user, $request->filled('remember'));
                return redirect()->route('cs.chat.index');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function logout(Request $request)
    {
        // Logout CS atau Admin dengan cara manual menghapus session guard spesifik
        // Jangan gunakan auth()->logout() karena akan mempengaruhi guard lain
        
        if (auth('cs')->check()) {
            // Hapus session authentication untuk guard CS saja
            $sessionKey = 'login_cs_' . sha1('App\Models\User');
            $request->session()->forget($sessionKey);
            $request->session()->forget('password_hash_cs');
            
            // Regenerate token untuk keamanan
            $request->session()->regenerateToken();
            
            return redirect('/login')->with('success', 'CS berhasil logout.');
            
        } elseif (auth('admin')->check()) {
            // Hapus session authentication untuk guard Admin saja
            $sessionKey = 'login_admin_' . sha1('App\Models\User');
            $request->session()->forget($sessionKey);
            $request->session()->forget('password_hash_admin');
            
            // Regenerate token untuk keamanan
            $request->session()->regenerateToken();
            
            return redirect('/login')->with('success', 'Admin berhasil logout.');
        }
        
        return redirect('/login');
    }

    public function memberLogout(Request $request)
    {
        // Logout khusus untuk Member (guard member)
        if (auth('member')->check()) {
            auth('member')->logout();
            $request->session()->regenerateToken();
        }
        
        return redirect('/member/login')->with('success', 'Anda berhasil logout.');
    }
}
