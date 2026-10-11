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
            'reason'             => ['required', 'string', 'max:500'],
            'checkout_compliant' => ['nullable', 'boolean'],
        ]);

        try {
            $this->service->end(
                $tenancy,
                $request->user(),
                $data['reason'],
                $data['checkout_compliant'] ?? null,
            );
        } catch (\DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()
            ->route('landlord.tenancies.show', $tenancy)
            ->with('status', 'Tenancy ended.');
    }
    public function recordPayment(Request $request, Tenancy $tenancy, \App\Services\PaymentService $payments): RedirectResponse
    {
        $this->authorize('view', $tenancy);

        $data = $request->validate([
            'amount'    => ['required', 'numeric', 'min:0.01', 'max:9999999'],
            'due_date'  => ['required', 'date'],
            'on_time'   => ['required', 'boolean'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes'     => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $payments->record(
                $tenancy,
                $request->user(),
                (float) $data['amount'],
                $data['due_date'],
                (bool) $data['on_time'],
                $data['reference'] ?? null,
                $data['notes'] ?? null,
            );
        } catch (\DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return back()->with('status', 'Payment recorded.');
    }
}