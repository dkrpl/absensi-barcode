<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetTimezone
{
    public function handle(Request $request, Closure $next)
    {
        // Set timezone to Asia/Jakarta
        date_default_timezone_set('Asia/Jakarta');

        return $next($request);
    }
}
