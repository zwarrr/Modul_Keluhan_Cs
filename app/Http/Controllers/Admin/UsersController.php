<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User as Users;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data dari tabel users (admin + cs)
        $usersFromUsersTable = Users::select('id', 'name', 'email', 'role', 'phone_number', 'created_at')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone_number' => $user->phone_number,
                    'created_at' => $user->created_at,
                    'table_source' => 'users', // untuk identifikasi sumber data
                ];
            });

        // Ambil data dari tabel members
        $membersFromMembersTable = Member::select('id', 'member_id', 'name', 'email', 'phone_number', 'created_at')
            ->get()
            ->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email ?? '-',
                    'role' => 'member',
                    'phone_number' => $member->phone_number,
                    'created_at' => $member->created_at,
                    'table_source' => 'members', // untuk identifikasi sumber data
                ];
            });

        // Gabungkan kedua collection
        $allUsers = $usersFromUsersTable->concat($membersFromMembersTable);

        // Urutkan: admin dulu, lalu cs, lalu member, kemudian berdasarkan created_at asc (terlama ke terbaru)
        $users = $allUsers->sortBy([
            function ($a, $b) {
                $roleOrder = ['admin' => 0, 'cs' => 1, 'member' => 2];
                return $roleOrder[$a['role']] <=> $roleOrder[$b['role']];
            },
            ['created_at', 'asc']
        ])->values();
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => $request->role === 'cs' ? 'required|email' : 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:member,cs',
            'phone_number' => 'nullable|string|max:20',
        ]);

        if ($request->role === 'member') {
            // Validasi email/member_id unique untuk members (jika diisi)
            $memberIdToUse = $request->email ?: null;
            
            if ($memberIdToUse) {
                $request->validate([
                    'email' => 'unique:members,member_id',
                ]);
            }

            // Simpan ke tabel members
            Member::create([
                'member_id' => $memberIdToUse ?: 'M' . str_pad(Member::max('id') + 1, 6, '0', STR_PAD_LEFT),
                'name' => $request->name,
                'email' => null, // Email tidak digunakan untuk member
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'address' => $request->address,
            ]);
        } else {
            // Validasi email unique untuk users (cs) - wajib diisi
            $request->validate([
                'email' => 'required|email|unique:users,email',
            ]);

            // Simpan ke tabel users
            Users::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone_number' => $request->phone_number,
            ]);
        }

        return redirect()->route('dataakuns.index')->with('success', 'Akun berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, Request $request)
    {
        $source = $request->get('source', 'users');
        
        if ($source === 'members') {
            $user = Member::findOrFail($id);
            $user->role = 'member'; // Set role untuk konsistensi di view
            $user->table_source = 'members';
        } else {
            $user = Users::findOrFail($id);
            $user->table_source = 'users';
        }
        
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $source = $request->get('source', 'users');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'password' => 'nullable|string|min:6|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'role' => 'nullable|in:admin,cs,member',
        ]);

        if ($source === 'members') {
            $user = Member::findOrFail($id);
            
            if ($request->email) {
                $request->validate([
                    'email' => 'unique:members,email,'.$id,
                ]);
            }
        } else {
            $user = Users::findOrFail($id);
            
            if ($request->email) {
                $request->validate([
                    'email' => 'unique:users,email,'.$id,
                ]);
            }
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ];

        // Update role jika ada dan bukan member (member tidak punya role di table users)
        if ($request->has('role') && $source === 'users') {
            $data['role'] = $request->role;
        }

        // Address hanya untuk members, tidak untuk users (CS/Admin)
        if ($source === 'members') {
            $data['address'] = $request->address;
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('dataakuns.index')->with('success', 'Akun berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $source = $request->get('source', 'users');
        
        if ($source === 'members') {
            $user = Member::findOrFail($id);
        } else {
            $user = Users::findOrFail($id);
            
            // Pastikan tidak menghapus akun admin
            if ($user->role === 'admin') {
                return redirect()->route('dataakuns.index')->with('error', 'Akun admin tidak dapat dihapus');
            }
        }

        $user->delete();

        return redirect()->route('dataakuns.index')->with('success', 'Akun berhasil dihapus');
    }
}
