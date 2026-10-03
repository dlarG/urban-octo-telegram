<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsRenter
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isRenter(), 403, 'Renter access required.');
        return $next($request);
    }
}
