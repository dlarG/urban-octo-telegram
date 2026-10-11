<?php
namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\BoardingHouse;
use App\Models\Favorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = $request->user()
            ->favorites()
            ->with(['boardingHouse.propertyImages'])
            ->latest()
            ->paginate(12);

        return view('renter.favorites.index', compact('favorites'));
    }

    public function toggle(Request $request, BoardingHouse $boarding_house): RedirectResponse
    {
        $user = $request->user();

        $existing = Favorite::where('user_id', $user->id)
            ->where('boarding_house_id', $boarding_house->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $msg = 'Removed from favorites.';
        } else {
            Favorite::create([
                'user_id'            => $user->id,
                'boarding_house_id'  => $boarding_house->id,
            ]);
            $msg = 'Added to favorites.';
        }

        return back()->with('status', $msg);
    }
}