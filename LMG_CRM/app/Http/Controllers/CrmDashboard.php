<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CrmDashboard extends Controller
{
    //
    public function index() {
        $title = "CRM | Dashboard";
        $user_name = session('user_name');
        $user_email = session('user_name');
        return view('user.crm.dashboard', compact('title', 'user_name', 'user_email'));
    }
}
