<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MarketingDashboard extends Controller
{
    //
    public function index() {
        $title = "Marketing | Dashboard";
        $user_name = session('user_name');
        $user_email = session('user_name');
        return view('user.marketing.dashboard', compact('title', 'user_name', 'user_email'));
    }
}
