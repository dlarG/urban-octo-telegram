<?php
namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Enums\RoomStatus;
use App\Models\Amenity;
use App\Models\BoardingHouse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPropertyController extends Controller
{
    public function index(Request $request): View
    {
        $query = BoardingHouse::query()
            ->where('status', PropertyStatus::Active)
            ->with(['propertyImages', 'amenities'])
            ->withCount(['rooms' => fn($q) => $q->where('status', RoomStatus::Available)]);

        // Filter: gender_policy
        if ($gender = $request->input('gender')) {
            $query->where('gender_policy', $gender);
        }

        // Filter: min/max price (via available rooms)
        if ($min = $request->input('min')) {
            $query->whereHas('rooms', fn($q) => $q->where('base_price_monthly', '>=', $min));
        }
        if ($max = $request->input('max')) {
            $query->whereHas('rooms', fn($q) => $q->where('base_price_monthly', '<=', $max));
        }

        // Filter: amenities (array)
        if ($amenities = $request->input('amenities', [])) {
            $query->whereHas('amenities', fn($q) => $q->whereIn('amenities.id', (array) $amenities));
        }

        // Filter: allows cooking
        if ($request->boolean('allows_cooking')) {
            $query->where('allows_cooking', true);
        }

        // Search by name
        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        // Near campus / near Sogod center (Haversine)
        if ($request->boolean('near')) {
            $lat = config('rentstreet.center_lat');
            $lng = config('rentstreet.center_lng');
            $query->near($lat, $lng, 5);
        }

        $houses = $query->latest()->paginate(12)->withQueryString();
        $amenitiesForFilter = Amenity::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('public.properties.index', compact('houses', 'amenitiesForFilter'));
    }

    public function show(BoardingHouse $boarding_house): View
    {
        abort_unless($boarding_house->status === PropertyStatus::Active, 404);

        $boarding_house->load([
            'amenities',
            'propertyImages',
            'rooms' => fn($q) => $q->where('status', RoomStatus::Available)->with('propertyImages'),
            'landlord.landlordProfile',
        ]);

        return view('public.properties.show', ['house' => $boarding_house]);
    }
}