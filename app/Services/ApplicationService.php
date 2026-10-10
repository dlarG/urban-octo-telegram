<?php
namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\RoomStatus;
use App\Models\RentalApplication;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
    /**
     * Renter applies to a room.
     *
     * Rules (from v1 §4):
     *   - Duplicate prevention is PER-ROOM, not per-property.
     *   - "Blocking" statuses: submitted, viewed, accepted.
     *   - Withdrawn/rejected applications do NOT block.
     */
    public function apply(User $renter, Room $room, ?string $message = null): RentalApplication
    {
        if (! $renter->isRenter()) {
            throw new \DomainException('Only renters can apply to rooms.');
        }

        if ($room->status !== RoomStatus::Available) {
            throw new \DomainException('This room is not currently accepting applications.');
        }

        if ($room->hasBlockingApplicationFrom($renter->id)) {
            throw new \DomainException('You already have an active application for this room.');
        }

        return DB::transaction(function () use ($renter, $room, $message) {
            return RentalApplication::create([
                'renter_id' => $renter->id,
                'room_id'   => $room->id,
                'status'    => ApplicationStatus::Submitted,
                'message'   => $message,
            ]);
        });
    }

    public function withdraw(RentalApplication $application, User $renter): RentalApplication
    {
        if ($application->renter_id !== $renter->id) {
            throw new \DomainException('You can only withdraw your own application.');
        }

        if (! in_array($application->status, [ApplicationStatus::Submitted, ApplicationStatus::Viewed], true)) {
            throw new \DomainException('This application can no longer be withdrawn.');
        }

        $application->update([
            'status'       => ApplicationStatus::Withdrawn,
            'responded_at' => now(),
        ]);

        return $application;
    }
}