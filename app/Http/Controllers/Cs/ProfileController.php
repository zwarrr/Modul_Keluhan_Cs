<?php

namespace App\Http\Controllers\Cs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::guard('cs')->user();
        return view('cs.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('cs')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        return redirect()->route('cs.profile.index')->with('success', 'Profil berhasil diupdate');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('cs')->user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Cek password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('cs.profile.index')->with('success', 'Password berhasil diupdate');
    }
}
