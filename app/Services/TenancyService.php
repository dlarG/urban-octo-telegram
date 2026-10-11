<?php
namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\RoomStatus;
use App\Enums\TenancyStatus;
use App\Models\RentalApplication;
use App\Models\Tenancy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TenancyService
{
    /**
     * Accept an application → creates a tenancy row.
     *
     * Rules:
     *  - The application must be in a state that can be accepted (submitted, viewed).
     *  - The landlord must own the property the room belongs to.
     *  - Creating a tenancy marks the application ACCEPTED and, optionally, the room FULL.
     *    (We only flip the room to FULL if the room's capacity is 1 — for shared rooms
     *     the landlord may accept multiple applicants. Landlord can override later.)
     *  - Every other blocking application on the same room is rejected automatically
     *    (they were competing for the same slot).
     */
    public function accept(RentalApplication $application, User $landlord, array $overrides = []): Tenancy
    {
        $this->guardLandlordOwnsApplication($application, $landlord);
        $this->guardApplicationAcceptable($application);

        return DB::transaction(function () use ($application, $landlord, $overrides) {
            $room = $application->room;

            $tenancy = Tenancy::create([
                'rental_application_id' => $application->id,
                'renter_id'             => $application->renter_id,
                'room_id'               => $room->id,
                'landlord_id'           => $landlord->id,
                'status'                => TenancyStatus::Active,
                'start_date'            => $overrides['start_date'] ?? now()->addDays(3)->toDateString(),
                'end_date'              => $overrides['end_date'] ?? null,
                'monthly_rent'          => $overrides['monthly_rent'] ?? $room->base_price_monthly,
            ]);

            $application->update([
                'status'         => ApplicationStatus::Accepted,
                'responded_at'   => now(),
                'response_message' => $overrides['response_message'] ?? null,
            ]);

            // Auto-reject other blocking applications on the same room
            RentalApplication::query()
                ->where('room_id', $room->id)
                ->where('id', '!=', $application->id)
                ->whereIn('status', [ApplicationStatus::Submitted, ApplicationStatus::Viewed])
                ->update([
                    'status'         => ApplicationStatus::Rejected,
                    'responded_at'   => now(),
                    'response_message' => 'Room was filled by another applicant.',
                ]);

            // Private rooms go FULL once tenanted; shared rooms stay AVAILABLE
            if ($room->capacity === 1) {
                $room->update(['status' => RoomStatus::Full]);
            }

            return $tenancy;
        });
    }

    public function reject(RentalApplication $application, User $landlord, string $reason): RentalApplication
    {
        $this->guardLandlordOwnsApplication($application, $landlord);
        $this->guardApplicationAcceptable($application);

        $application->update([
            'status'           => ApplicationStatus::Rejected,
            'responded_at'     => now(),
            'response_message' => $reason,
        ]);

        return $application;
    }

    /**
     * End an active tenancy (mutually agreed, renter moved out, etc.)
     */
    public function end(
        Tenancy $tenancy,
        User $landlord,
        string $reason,
        ?bool $checkoutCompliant = null,
    ): Tenancy {
        if ($tenancy->landlord_id !== $landlord->id) {
            throw new \DomainException('You do not own this tenancy.');
        }
        if (! $tenancy->isActive()) {
            throw new \DomainException('Only active tenancies can be ended.');
        }

        return DB::transaction(function () use ($tenancy, $landlord, $reason, $checkoutCompliant) {
            $tenancy->update([
                'status'             => TenancyStatus::Completed,
                'completed_at'       => now(),
                'termination_reason' => $reason,
            ]);

            // Free the room back up
            $room = $tenancy->room;
            if ($room->status === RoomStatus::Full) {
                $room->update(['status' => RoomStatus::Available]);
            }

            // Fire checkout trust event if rating provided
            if ($checkoutCompliant !== null) {
                app(TrustScoreService::class)->record(
                    user: $tenancy->renter,
                    type: $checkoutCompliant
                        ? \App\Enums\TrustEventType::CheckoutCompliant
                        : \App\Enums\TrustEventType::CheckoutViolation,
                    reason: $checkoutCompliant
                        ? "Compliant checkout for {$room->room_label}"
                        : "Checkout violation for {$room->room_label}: {$reason}",
                    tenancyId: $tenancy->id,
                    createdBy: $landlord->id,
                );
            }

            return $tenancy;
        });
    }

    protected function guardLandlordOwnsApplication(RentalApplication $application, User $landlord): void
    {
        $owner = $application->room->boardingHouse->landlord_id;
        if ($owner !== $landlord->id) {
            throw new \DomainException('This application does not belong to one of your properties.');
        }
    }

    protected function guardApplicationAcceptable(RentalApplication $application): void
    {
        if (! in_array($application->status, [ApplicationStatus::Submitted, ApplicationStatus::Viewed], true)) {
            throw new \DomainException('This application can no longer be actioned.');
        }
    }
}