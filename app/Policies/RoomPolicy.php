<?php
namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function view(User $user, Room $room): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isLandlord()
            && $room->boardingHouse->landlord_id === $user->id;
    }

    public function create(User $user): bool
    {
        // Landlord can only create rooms on their own properties.
        // Ownership check is done in the controller via the parent house.
        return $user->isLandlord();
    }

    public function update(User $user, Room $room): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isLandlord()
            && $room->boardingHouse->landlord_id === $user->id;
    }

    public function delete(User $user, Room $room): bool
    {
        if ($user->isAdmin()) return true;
        if (! $user->isLandlord()) return false;
        if ($room->boardingHouse->landlord_id !== $user->id) return false;
        // Block deleting rooms that have active tenancies
        return $room->tenancies()->where('status', 'active')->count() === 0;
    }
}