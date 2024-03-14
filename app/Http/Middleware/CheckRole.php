<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Mst_User_Type;
use Illuminate\Http\Request;


class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$role)
    {
        if (!auth()->check()) {
            if (in_array(1, $role)) {
                return redirect()->route('mst_login');
            } elseif (in_array(96, $role)) {
                return redirect()->route('mst_login.pharmacy');
            } elseif (in_array(18, $role)) {
                return redirect()->route('mst_login.receptionist');
            } elseif (in_array(20, $role)) {
                return redirect()->route('mst_login.doctor');
            }elseif (in_array(21, $role)) {
                    return redirect()->route('mst_login.accountant');
            } else {
                return redirect()->route('mst_login');
            }
        }

        $userRole = auth()->user()->user_type_id;
            if (!in_array($userRole, $role)) {
                abort(403);
            }

        return $next($request);
    }
}
