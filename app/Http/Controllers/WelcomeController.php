<?php

namespace App\Http\Controllers;

use App\Models\HostingPlan;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $plans = HostingPlan::all();
        return view('welcome', compact('plans'));
    }

    public function privacy()
    {
        return view('legal.privacy');
    }
}
