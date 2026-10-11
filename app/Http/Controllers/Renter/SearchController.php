<?php
namespace App\Http\Controllers\Renter;

use App\Enums\PropertyStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\BoardingHouse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Enums\ApplicationStatus;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = BoardingHouse::query()
            ->where('status', PropertyStatus::Active)
            ->with(['propertyImages'])
            ->withCount(['rooms' => fn($q) => $q->where('status', RoomStatus::Available)])
            ->withMin(['rooms' => fn($q) => $q->where('status', RoomStatus::Available)], 'base_price_monthly')
            ->withMax(['rooms' => fn($q) => $q->where('status', RoomStatus::Available)], 'base_price_monthly');

        // -------- Filters --------
        if ($gender = $request->input('gender')) {
            $query->where('gender_policy', $gender);
        }

        if ($min = $request->input('min_price')) {
            $query->whereHas('rooms', fn($q) => $q
                ->where('status', RoomStatus::Available)
                ->where('base_price_monthly', '>=', $min));
        }
        if ($max = $request->input('max_price')) {
            $query->whereHas('rooms', fn($q) => $q
                ->where('status', RoomStatus::Available)
                ->where('base_price_monthly', '<=', $max));
        }

        if ($amenities = $request->input('amenities', [])) {
            $query->whereHas('amenities', fn($q) => $q->whereIn('amenities.id', (array) $amenities));
        }

        if ($request->boolean('allows_cooking')) {
            $query->where('allows_cooking', true);
        }

        if ($request->boolean('sub_metered')) {
            $query->where('is_sub_metered', true);
        }

        if ($q = $request->input('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('barangay', 'like', "%{$q}%")
                  ->orWhere('address_line', 'like', "%{$q}%");
            });
        }

        // Distance from Sogod center
        $radius = (float) $request->input('radius', 0);
        if ($radius > 0) {
            $query->near(
                config('rentstreet.center_lat'),
                config('rentstreet.center_lng'),
                $radius
            );
        }

        // Sorting
        match ($request->input('sort', 'newest')) {
            'price_asc'  => $query->orderBy('name'),
            'newest'     => $query->latest(),
            default      => $query->latest(),
        };

        $houses = $query->paginate(12)->withQueryString();

        // Stats for the renter's own application state
        $renter             = $request->user();
        $hasActiveTenancy   = $renter->hasActiveTenancy();
        $activeTenancy      = $hasActiveTenancy ? $renter->activeTenancy() : null;

        // Which rooms has the renter already applied to?
        $appliedRoomIds = $renter->applications()
            ->whereIn('status', [
                \App\Enums\ApplicationStatus::Submitted,
                \App\Enums\ApplicationStatus::Viewed,
                \App\Enums\ApplicationStatus::Accepted,
            ])
            ->pluck('room_id')
            ->all();

        $amenitiesForFilter = Amenity::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('renter.search.index', compact(
            'houses',
            'amenitiesForFilter',
            'appliedRoomIds',
            'hasActiveTenancy',
            'activeTenancy',
        ));
    }
    public function show(Request $request, BoardingHouse $boarding_house): View
    {
        abort_unless($boarding_house->status === PropertyStatus::Active, 404);

        $boarding_house->load([
            'amenities',
            'propertyImages',
            'rooms' => fn($q) => $q->where('status', RoomStatus::Available)->with('propertyImages'),
            'landlord.landlordProfile',
        ]);

        $renter           = $request->user();
        $hasActiveTenancy = $renter->hasActiveTenancy();
        $activeTenancy    = $hasActiveTenancy ? $renter->activeTenancy() : null;

        $appliedRoomIds = $renter->applications()
            ->whereIn('status', [
                \App\Enums\ApplicationStatus::Submitted,
                \App\Enums\ApplicationStatus::Viewed,
                \App\Enums\ApplicationStatus::Accepted,
            ])
            ->pluck('room_id')
            ->all();

        $isFavorited = $renter->favorites()
            ->where('boarding_house_id', $boarding_house->id)
            ->exists();

        return view('renter.search.show', compact(
            'boarding_house',
            'appliedRoomIds',
            'hasActiveTenancy',
            'activeTenancy',
            'isFavorited',
        ));
    }
}