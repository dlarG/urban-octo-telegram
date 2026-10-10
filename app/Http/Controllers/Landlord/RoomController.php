<?php
namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\StoreRoomRequest;
use App\Http\Requests\Landlord\UpdateRoomRequest;
use App\Models\BoardingHouse;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(BoardingHouse $boarding_house): View
    {
        $this->authorize('view', $boarding_house);

        $rooms = $boarding_house->rooms()->withCount('propertyImages')->latest()->get();

        return view('landlord.rooms.index', [
            'house' => $boarding_house,
            'rooms' => $rooms,
        ]);
    }

    public function create(BoardingHouse $boarding_house): View
    {
        $this->authorize('view', $boarding_house);

        return view('landlord.rooms.create', ['house' => $boarding_house]);
    }

    public function store(StoreRoomRequest $request, BoardingHouse $boarding_house): RedirectResponse
    {
        $this->authorize('view', $boarding_house);

        $room = $boarding_house->rooms()->create($request->validated());

        return redirect()
            ->route('landlord.properties.rooms.show', [$boarding_house, $room])
            ->with('status', 'Room created. Add photos next.');
    }

    public function show(BoardingHouse $boarding_house, Room $room): View
    {
        $this->authorize('view', $room);
        abort_unless($room->boarding_house_id === $boarding_house->id, 404);

        $room->load('propertyImages');

        return view('landlord.rooms.show', [
            'house' => $boarding_house,
            'room'  => $room,
        ]);
    }

    public function edit(BoardingHouse $boarding_house, Room $room): View
    {
        $this->authorize('update', $room);
        abort_unless($room->boarding_house_id === $boarding_house->id, 404);

        return view('landlord.rooms.edit', [
            'house' => $boarding_house,
            'room'  => $room,
        ]);
    }

    public function update(UpdateRoomRequest $request, BoardingHouse $boarding_house, Room $room): RedirectResponse
    {
        abort_unless($room->boarding_house_id === $boarding_house->id, 404);

        $room->update($request->validated());

        return redirect()
            ->route('landlord.properties.rooms.show', [$boarding_house, $room])
            ->with('status', 'Room updated.');
    }

    public function destroy(BoardingHouse $boarding_house, Room $room): RedirectResponse
    {
        $this->authorize('delete', $room);
        abort_unless($room->boarding_house_id === $boarding_house->id, 404);

        $room->delete();

        return redirect()
            ->route('landlord.properties.rooms.index', $boarding_house)
            ->with('status', 'Room deleted.');
    }
}