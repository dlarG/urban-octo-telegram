<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsLandlord
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isLandlord(), 403, 'Landlord access required.');
        return $next($request);
    }
}
