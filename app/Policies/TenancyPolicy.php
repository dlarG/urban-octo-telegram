<?php
namespace App\Policies;

use App\Models\Tenancy;
use App\Models\User;

class TenancyPolicy
{
    public function view(User $user, Tenancy $tenancy): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isRenter() && $tenancy->renter_id === $user->id) return true;
        if ($user->isLandlord() && $tenancy->landlord_id === $user->id) return true;
        return false;
    }

    public function end(User $user, Tenancy $tenancy): bool
    {
        return $user->isLandlord() && $tenancy->landlord_id === $user->id && $tenancy->isActive();
    }
}