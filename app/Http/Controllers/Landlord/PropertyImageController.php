<?php
namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\BoardingHouse;
use App\Models\PropertyImage;
use App\Models\Room;
use App\Services\PropertyImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PropertyImageController extends Controller
{
    public function __construct(protected PropertyImageService $images) {}

    public function storeForHouse(Request $request, BoardingHouse $boarding_house): RedirectResponse
    {
        $this->authorize('update', $boarding_house);

        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $this->images->attach($boarding_house, $request->file('image'));

        return back()->with('status', 'Photo uploaded.');
    }

    public function storeForRoom(Request $request, BoardingHouse $boarding_house, Room $room): RedirectResponse
    {
        $this->authorize('update', $room);
        abort_unless($room->boarding_house_id === $boarding_house->id, 404);

        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $this->images->attach($room, $request->file('image'));

        return back()->with('status', 'Photo uploaded.');
    }

    public function destroy(PropertyImage $image): RedirectResponse
    {
        // Determine parent and re-check ownership
        if ($image->boarding_house_id) {
            $this->authorize('update', $image->boardingHouse);
        } elseif ($image->room_id) {
            $this->authorize('update', $image->room);
        }

        $this->images->delete($image);

        return back()->with('status', 'Photo removed.');
    }

    public function makePrimary(PropertyImage $image): RedirectResponse
    {
        if ($image->boarding_house_id) {
            $this->authorize('update', $image->boardingHouse);
        } elseif ($image->room_id) {
            $this->authorize('update', $image->room);
        }

        $this->images->makePrimary($image);

        return back()->with('status', 'Cover photo updated.');
    }
}