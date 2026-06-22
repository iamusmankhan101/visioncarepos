<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HostingExpired
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('/') || $request->is('')) {
            return $next($request);
        }

        if (Auth::check()) {
            Auth::logout();
            $request->session()->flush();
        }

        return redirect('/');
    }
}
