<?php
namespace App\Http\Controllers\Landlord;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\RentalApplication;
use App\Services\TenancyService;
use App\Services\TrustScoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(
        protected TenancyService $tenancies,
        protected TrustScoreService $trust,
    ) {}

    public function index(Request $request): View
    {
        $landlord = $request->user();
        $filter   = $request->input('status', 'pending');

        $query = RentalApplication::query()
            ->whereHas('room.boardingHouse', fn($q) => $q->where('landlord_id', $landlord->id))
            ->with(['room.boardingHouse', 'renter.renterProfile', 'renter.trustScore']);

        match ($filter) {
            'pending'  => $query->whereIn('status', [ApplicationStatus::Submitted, ApplicationStatus::Viewed]),
            'accepted' => $query->where('status', ApplicationStatus::Accepted),
            'rejected' => $query->where('status', ApplicationStatus::Rejected),
            'all'      => null,
        };

        $applications = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'pending'  => RentalApplication::query()->whereHas('room.boardingHouse',
                fn($q) => $q->where('landlord_id', $landlord->id))
                ->whereIn('status', [ApplicationStatus::Submitted, ApplicationStatus::Viewed])->count(),
            'accepted' => RentalApplication::query()->whereHas('room.boardingHouse',
                fn($q) => $q->where('landlord_id', $landlord->id))
                ->where('status', ApplicationStatus::Accepted)->count(),
            'rejected' => RentalApplication::query()->whereHas('room.boardingHouse',
                fn($q) => $q->where('landlord_id', $landlord->id))
                ->where('status', ApplicationStatus::Rejected)->count(),
        ];

        return view('landlord.applications.index', compact('applications', 'filter', 'counts'));
    }

    public function show(Request $request, RentalApplication $application): View
    {
        $this->authorize('view', $application);

        // Mark as viewed if still submitted
        if ($application->status === ApplicationStatus::Submitted) {
            $application->update([
                'status'    => ApplicationStatus::Viewed,
                'viewed_at' => now(),
            ]);
        }

        $application->load(['room.boardingHouse', 'renter.renterProfile.campus', 'renter.trustScore']);

        // Trust score: only readable with a real application + logged
        $trustScore = null;
        $renter = $application->renter;
        if ($renter->trustScore) {
            // Already checked by authorize + real application exists
            $this->trust->logAccess(
                viewer: $request->user(),
                subject: $renter,
                applicationId: $application->id,
                ip: $request->ip(),
            );
            $trustScore = $renter->trustScore;
        }

        $recentEvents = $renter->trustScoreEvents()->latest()->take(5)->get();

        return view('landlord.applications.show', compact('application', 'trustScore', 'recentEvents'));
    }

    public function accept(Request $request, RentalApplication $application): RedirectResponse
    {
        $this->authorize('act', $application);

        $data = $request->validate([
            'start_date'       => ['nullable', 'date', 'after_or_equal:today'],
            'monthly_rent'     => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'response_message' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $tenancy = $this->tenancies->accept($application, $request->user(), $data);
        } catch (\DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()
            ->route('landlord.applications.show', $application)
            ->with('status', 'Application accepted. Tenancy created.');
    }

    public function reject(Request $request, RentalApplication $application): RedirectResponse
    {
        $this->authorize('act', $application);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            $this->tenancies->reject($application, $request->user(), $data['reason']);
        } catch (\DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()
            ->route('landlord.applications.index')
            ->with('status', 'Application rejected.');
    }
}