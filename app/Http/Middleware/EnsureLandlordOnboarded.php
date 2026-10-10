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

        // Allow the profile page (for resubmission after rejection)
        if ($request->routeIs('landlord.profile') || $request->routeIs('landlord.profile.update')) {
            return $next($request);
        }

        if (! $profile || ! $profile->hasSubmittedDocuments()) {
            return redirect()->route('landlord.profile')
                ->with('status', 'Please submit your documents before continuing.');
        }

        return $next($request);
    }
}