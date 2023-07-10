<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $pageTitle="Dashboard";
        return view('elements.dashboard.dashboard',compact('pageTitle'));

    }
}
