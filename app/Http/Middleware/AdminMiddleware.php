<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user existance
        // if (!$request->user()) {
        //     return redirect()->route('mst_login')->with('failed', 'An error occurred: Unable to locate the user. Please try again.');
        // }

        // Check if the user is an admin
        if ($request->user() && $request->user()->user_type_id === '1') {
            return $next($request);
        }
        // if ($request->user() && $request->user()->user_type_id != '1') {
        //     return redirect('/login')->with('failed', 'You do not have admin privileges.');
        // }

        // If not an admin, redirect or return an error response
        return 1;//redirect('/login')->with('failed', 'You need to log in to access this page.');
    }
}
