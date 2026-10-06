<?php
namespace App\Services;

use App\Enums\PropertyStatus;
use App\Models\BoardingHouse;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BoardingHouseService
{
    /**
     * Create a new boarding house. New properties always start pending_review
     * regardless of what the landlord passes in.
     */
    public function create(User $landlord, array $data): BoardingHouse
    {
        return DB::transaction(function () use ($landlord, $data) {
            $house = new BoardingHouse($data);
            $house->landlord_id       = $landlord->id;
            $house->status            = PropertyStatus::PendingReview;
            $house->status_updated_at = now();
            $house->status_updated_by = $landlord->id; // initial submission
            $house->save();

            if (! empty($data['amenities'])) {
                $house->amenities()->sync($data['amenities']);
            }

            return $house;
        });
    }

    /**
     * Update an existing property with the v1 rule:
     * "any edit to an inactive property auto-flips it back to pending_review."
     * Also handles active → pending_review on substantive edits (recommended).
     */
    public function update(BoardingHouse $house, array $data): BoardingHouse
    {
        if ($house->status === PropertyStatus::Suspended) {
            throw new \DomainException('Suspended properties cannot be edited.');
        }

        return DB::transaction(function () use ($house, $data) {
            $house->fill($data);

            // Rule: edits reset review state for anything that was inactive.
            // Active and pending_review keep their status (active stays active unless admin acts);
            // only inactive → pending_review per v1 §4.
            if ($house->status === PropertyStatus::Inactive) {
                $house->status = PropertyStatus::PendingReview;
                $house->status_updated_at = now();
            }

            $house->save();

            if (array_key_exists('amenities', $data)) {
                $house->amenities()->sync($data['amenities'] ?? []);
            }

            return $house;
        });
    }

    public function delete(BoardingHouse $house): void
    {
        if ($house->status === PropertyStatus::Suspended) {
            throw new \DomainException('Suspended properties cannot be deleted.');
        }
        $house->delete();
    }
}