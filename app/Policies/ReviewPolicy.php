<?php
namespace App\Policies;

use App\Models\Review;
use App\Models\Tenancy;
use App\Models\User;

class ReviewPolicy
{
    public function create(User $user, Tenancy $tenancy): bool
    {
        return $user->isRenter()
            && $tenancy->renter_id === $user->id
            && $tenancy->isCompleted()          // must be completed, not active
            && ! $tenancy->review()->exists();   // only one review per tenancy
    }

    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->renter_id;
    }
}