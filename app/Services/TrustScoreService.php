<?php
namespace App\Services;

use App\Enums\TrustEventType;
use App\Models\TrustScore;
use App\Models\TrustScoreEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TrustScoreService
{
    /**
     * Called INSIDE the registration transaction. Creates the empty ledger row.
     * Missing row = integrity error. Never default to "trusted" on read.
     */
    public function initializeFor(User $user): TrustScore
    {
        return TrustScore::create([
            'user_id'       => $user->id,
            'score'         => config('rentstreet.trust_score.initial'),
            'last_event_at' => null,
        ]);
    }

    /**
     * Append a trust event and update the cached score.
     * The ONLY way trust score changes. Never `update()` a score directly.
     */
    public function record(
        User $user,
        TrustEventType $type,
        ?float $deltaOverride = null,
        ?string $reason = null,
        ?int $tenancyId = null,
        ?int $paymentId = null,
        ?int $disputeId = null,
        ?int $createdBy = null,
    ): TrustScoreEvent {
        return DB::transaction(function () use (
            $user, $type, $deltaOverride, $reason, $tenancyId, $paymentId, $disputeId, $createdBy
        ) {
            // Lock the score row to avoid races
            $score = TrustScore::where('user_id', $user->id)->lockForUpdate()->first();

            if (! $score) {
                throw new \RuntimeException(
                    "Trust score row missing for user {$user->id}. Integrity error — cannot record event."
                );
            }

            $delta      = $deltaOverride ?? $type->defaultDelta();
            $newScore   = max(
                config('rentstreet.trust_score.min'),
                min(config('rentstreet.trust_score.max'), (float) $score->score + $delta)
            );
            $appliedDelta = $newScore - (float) $score->score; // clamp-aware

            $event = TrustScoreEvent::create([
                'user_id'     => $user->id,
                'tenancy_id'  => $tenancyId,
                'payment_id'  => $paymentId,
                'dispute_id'  => $disputeId,
                'event_type'  => $type,
                'delta'       => $appliedDelta,
                'score_after' => $newScore,
                'reason'      => $reason,
                'created_by'  => $createdBy,
            ]);

            $score->forceFill([
                'score'         => $newScore,
                'last_event_at' => now(),
            ])->save();

            return $event;
        });
    }

    /**
     * RA 10173: "no public shaming" — landlord may only read a renter's score
     * if a real application exists between them, and every read is logged.
     */
    public function readableByLandlord(User $renter, User $landlord, int $applicationId): bool
    {
        return \App\Models\RentalApplication::query()
            ->where('id', $applicationId)
            ->where('renter_id', $renter->id)
            ->whereHas('room.boardingHouse', fn($q) => $q->where('landlord_id', $landlord->id))
            ->exists();
    }

    public function logAccess(User $viewer, User $subject, int $applicationId, ?string $ip): void
    {
        \App\Models\TrustScoreAccessLog::create([
            'viewer_id'             => $viewer->id,
            'subject_id'            => $subject->id,
            'rental_application_id' => $applicationId,
            'ip_address'            => $ip,
            'viewed_at'             => now(),
        ]);
    }
    public function resolveDispute(
        \App\Models\Dispute $dispute,
        User $admin,
        bool $uphold,
        string $note,
    ): void {
        if (! $admin->isAdmin()) {
            throw new \DomainException('Only admins can resolve disputes.');
        }
        if ($dispute->status !== \App\Enums\DisputeStatus::Open) {
            throw new \DomainException('This dispute has already been resolved.');
        }

        DB::transaction(function () use ($dispute, $admin, $uphold, $note) {
            if ($uphold) {
                // Counter the original negative event with a positive adjustment
                $this->record(
                    user: $dispute->user,
                    type: \App\Enums\TrustEventType::DisputeUpheld,
                    reason: "Dispute upheld: {$note}",
                    disputeId: $dispute->id,
                    createdBy: $admin->id,
                );
            }

            $dispute->update([
                'status'          => $uphold ? \App\Enums\DisputeStatus::Resolved : \App\Enums\DisputeStatus::Rejected,
                'resolved_by'     => $admin->id,
                'resolved_at'     => now(),
                'resolution_note' => $note,
            ]);
        });
    }
}