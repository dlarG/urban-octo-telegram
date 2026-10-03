<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLandlordOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isLandlord()) {
            return $next($request);
        }

        $profile = $user->landlordProfile;

        // Allow onboarding route itself, else infinite redirect
        if ($request->routeIs('landlord.onboarding') || $request->routeIs('landlord.onboarding.store')) {
            return $next($request);
        }

        if (! $profile || ! $profile->isOnboardingComplete()) {
            return redirect()->route('landlord.onboarding')
                ->with('status', 'Please complete your landlord profile before continuing.');
        }

        return $next($request);
    }
}