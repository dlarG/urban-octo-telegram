<?php
namespace App\Policies;

use App\Models\RentalApplication;
use App\Models\User;

class RentalApplicationPolicy
{
    public function view(User $user, RentalApplication $application): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isRenter() && $application->renter_id === $user->id) return true;
        if ($user->isLandlord()) {
            return $application->room->boardingHouse->landlord_id === $user->id;
        }
        return false;
    }

    public function act(User $user, RentalApplication $application): bool
    {
        return $user->isLandlord()
            && $application->room->boardingHouse->landlord_id === $user->id;
    }
}