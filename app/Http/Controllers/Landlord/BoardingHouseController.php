<?php
namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\StoreBoardingHouseRequest;
use App\Http\Requests\Landlord\UpdateBoardingHouseRequest;
use App\Models\Amenity;
use App\Models\BoardingHouse;
use App\Services\BoardingHouseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BoardingHouseController extends Controller
{
    public function __construct(protected BoardingHouseService $service) {}

    public function index(): View
    {
        $this->authorize('viewAny', BoardingHouse::class);

        $houses = auth()->user()->boardingHouses()
            ->withCount('rooms')
            ->latest()
            ->paginate(12);

        return view('landlord.properties.index', compact('houses'));
    }

    public function create(): View
    {
        $this->authorize('create', BoardingHouse::class);

        $amenities = Amenity::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('landlord.properties.create', compact('amenities'));
    }

    public function store(StoreBoardingHouseRequest $request): RedirectResponse
    {
        $house = $this->service->create($request->user(), $request->validated());

        return redirect()
            ->route('landlord.properties.show', $house)
            ->with('status', 'Property submitted for review.');
    }

    public function show(BoardingHouse $boarding_house): View
    {
        $this->authorize('view', $boarding_house);

        $boarding_house->load(['amenities', 'rooms', 'propertyImages']);

        return view('landlord.properties.show', ['house' => $boarding_house]);
    }

    public function edit(BoardingHouse $boarding_house): View
    {
        $this->authorize('update', $boarding_house);

        $amenities = Amenity::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('landlord.properties.edit', [
            'house'     => $boarding_house,
            'amenities' => $amenities,
        ]);
    }

    public function update(UpdateBoardingHouseRequest $request, BoardingHouse $boarding_house): RedirectResponse
    {
        try {
            $this->service->update($boarding_house, $request->validated());
        } catch (\DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()
            ->route('landlord.properties.show', $boarding_house)
            ->with('status', 'Property updated.');
    }

    public function destroy(BoardingHouse $boarding_house): RedirectResponse
    {
        $this->authorize('delete', $boarding_house);

        try {
            $this->service->delete($boarding_house);
        } catch (\DomainException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()
            ->route('landlord.properties.index')
            ->with('status', 'Property deleted.');
    }
}