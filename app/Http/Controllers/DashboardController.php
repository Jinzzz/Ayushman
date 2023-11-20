<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //  dd('welcome to dashboard');
        $pageTitle="Dashboard";
        return view('home',compact('pageTitle'));
    }
}
