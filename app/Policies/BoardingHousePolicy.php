<?php
namespace App\Policies;

use App\Enums\LandlordApprovalStatus;
use App\Enums\PropertyStatus;
use App\Models\BoardingHouse;
use App\Models\User;

class BoardingHousePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isLandlord() || $user->isAdmin();
    }

    public function view(User $user, BoardingHouse $house): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isLandlord() && $house->landlord_id === $user->id;
    }

    public function create(User $user): bool
    {
        if (! $user->isLandlord()) return false;

        $profile = $user->landlordProfile;

        return $profile
            && $profile->hasSubmittedDocuments() 
            && $profile->approval_status === LandlordApprovalStatus::Accepted;
    }

    public function update(User $user, BoardingHouse $house): bool
    {
        if ($user->isAdmin()) return true;
        if (! $user->isLandlord() || $house->landlord_id !== $user->id) return false;
        // Suspended properties cannot be edited by the landlord
        return $house->status !== PropertyStatus::Suspended;
    }

    public function delete(User $user, BoardingHouse $house): bool
    {
        if ($user->isAdmin()) return true;
        if (! $user->isLandlord() || $house->landlord_id !== $user->id) return false;
        return $house->status !== PropertyStatus::Suspended;
    }
}