<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelecallerDashboard extends Controller
{
    //
    public function index() {
        $title = "Telecaller | Dashboard";
        $user_name = session()->get('user_name');
        $user_email = session()->get('user_email');
        // dd(session()->all());
        // dd(Auth::guard('front')->user());
        return view('user.telecaller.dashboard', compact('title', 'user_name', 'user_email'));
    }
}
