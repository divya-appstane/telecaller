<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class AdminDashboard extends Controller
{
    public function index() 
    {
        $title = "Admin | Dashboard";
        $user_name = session('user_name');
        $user_email = session('user_name');
        return view('user.admin.dashboard', compact('title', 'user_name', 'user_email'));
    }
}
