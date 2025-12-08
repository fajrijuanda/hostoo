<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $domain = \App\Models\Domain::where('user_id', Auth::id())->first();
        return view('dashboard.index', compact('domain'));
    }
}
