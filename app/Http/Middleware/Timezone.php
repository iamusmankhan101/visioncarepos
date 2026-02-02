<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Timezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $timezone = config('app.timezone');

        try {
            if (session()->has('business.time_zone')) {
                $timezone = $request->session()->get('business.time_zone');
            } elseif (Auth::check() && Auth::user()->business) {
                $business = Auth::user()->business;
                $timezone = $business->time_zone ?? 'UTC';
            }

            // Validate timezone before setting
            if (!empty($timezone) && in_array($timezone, timezone_identifiers_list())) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }
        } catch (\Exception $e) {
            // If anything fails, use default timezone
            $timezone = config('app.timezone', 'UTC');
            date_default_timezone_set($timezone);
        }

        return $next($request);
    }
}
