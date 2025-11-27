<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function member()
    {
        return view('admin.users.member.index');
    }

    public function cs()
    {
        return view('admin.users.cs.index');
    }
}
