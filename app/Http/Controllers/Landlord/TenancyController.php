<?php
namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Tenancy;
use App\Services\TenancyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenancyController extends Controller
{
    public function __construct(protected TenancyService $service) {}

    public function index(Request $request): View
    {
        $tenancies = Tenancy::query()
            ->where('landlord_id', $request->user()->id)
            ->with(['renter', 'room.boardingHouse'])
            ->latest()
            ->paginate(15);

        return view('landlord.tenancies.index', compact('tenancies'));
    }

    public function show(Request $request, Tenancy $tenancy): View
    {
        $this->authorize('view', $tenancy);
        $tenancy->load(['renter.renterProfile', 'room.boardingHouse', 'payments', 'review']);

        return view('landlord.tenancies.show', compact('tenancy'));
    }

    public function end(Request $request, Tenancy $tenancy): RedirectResponse
    {
        $this->authorize('end', $tenancy);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            $this->service->end($tenancy, $request->user(), $data['reason']);
        } catch (\DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()
            ->route('landlord.tenancies.show', $tenancy)
            ->with('status', 'Tenancy ended.');
    }
}