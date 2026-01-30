<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBusinessSelection
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
        // Skip check for business selection routes and logout
        if ($request->routeIs(['business.select', 'business.register', 'business.store', 'business.switch', 'logout'])) {
            return $next($request);
        }

        // Skip check for API routes and AJAX requests
        if ($request->is('api/*') || $request->ajax()) {
            return $next($request);
        }

        $user = auth()->user();
        
        // If user is not authenticated, continue
        if (!$user) {
            return $next($request);
        }

        // Always redirect to business selection if user hasn't selected a business in this session
        // This ensures users go through business selection every time they login
        if (!session()->has('selected_business_id') || !$user->business_id) {
            return redirect()->route('business.select');
        }

        // If user's business is not active, redirect to business selection
        if ($user->business && !$user->business->is_active) {
            return redirect()->route('business.select')->with('error', 'Your current business is inactive. Please select another business.');
        }

        return $next($request);
    }
}