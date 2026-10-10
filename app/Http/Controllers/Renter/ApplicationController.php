<?php
namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\RentalApplication;
use App\Models\Room;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(protected ApplicationService $service) {}

    public function index(Request $request): View
    {
        $applications = RentalApplication::query()
            ->where('renter_id', $request->user()->id)
            ->with(['room.boardingHouse.propertyImages', 'room.propertyImages'])
            ->latest()
            ->paginate(12);

        return view('renter.applications.index', compact('applications'));
    }
    public function store(Request $request, Room $room): RedirectResponse
    {
        $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->service->apply(
                $request->user(),
                $room,
                $request->input('message'),
            );
        } catch (\DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()
            ->route('renter.applications')
            ->with('status', 'Application submitted. The landlord will review it soon.');
    }

    public function withdraw(Request $request, RentalApplication $application): RedirectResponse
    {
        try {
            $this->service->withdraw($application, $request->user());
        } catch (\DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return back()->with('status', 'Application withdrawn.');
    }
}