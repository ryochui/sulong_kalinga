<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        // First, check the main role (administrator, care_manager, care_worker)
        if ($role === 'administrator' && Auth::user()->role_id == 1) {
            return $next($request);
        }
        
        if ($role === 'care_manager' && Auth::user()->role_id == 2) {
            return $next($request);
        }
        
        if ($role === 'care_worker' && Auth::user()->role_id == 3) {
            return $next($request);
        }
        
        // Special check for executive_director (organization_role_id = 1)
        if ($role === 'executive_director' && Auth::user()->role_id == 1 && Auth::user()->organization_role_id == 1) {
            return $next($request);
        }

        if ($request->is('manager/*') && $role === 'care_manager' && Auth::user()->role_id == 2) {
            return $next($request);
        }

        // If we get here, the user doesn't have the required role
        return redirect()->back()->with('error', 'You do not have permission to access this page.');
    }
}