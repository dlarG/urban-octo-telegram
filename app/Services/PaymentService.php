<?php
namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\TrustEventType;
use App\Models\Payment;
use App\Models\Tenancy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(protected TrustScoreService $trust) {}

    /**
     * Landlord records a payment (on-time or late).
     * Sets paid_at + status, then fires the appropriate trust event.
     */
    public function record(
        Tenancy $tenancy,
        User $landlord,
        float $amount,
        string $dueDate,          // Y-m-d
        bool $onTime = true,
        ?string $reference = null,
        ?string $notes = null,
    ): Payment {
        if ($tenancy->landlord_id !== $landlord->id) {
            throw new \DomainException('You do not own this tenancy.');
        }
        if (! $tenancy->isActive()) {
            throw new \DomainException('Payments can only be recorded on active tenancies.');
        }

        return DB::transaction(function () use ($tenancy, $landlord, $amount, $dueDate, $onTime, $reference, $notes) {
            $payment = Payment::create([
                'tenancy_id' => $tenancy->id,
                'amount'     => $amount,
                'due_date'   => $dueDate,
                'paid_at'    => now()->toDateString(),
                'status'     => $onTime ? PaymentStatus::Paid : PaymentStatus::Late,
                'reference'  => $reference,
                'notes'      => $notes,
            ]);

            // Fire the trust event — this is why the payment exists.
            $this->trust->record(
                user: $tenancy->renter,
                type: $onTime ? TrustEventType::PaymentOnTime : TrustEventType::PaymentLate,
                reason: $onTime
                    ? "On-time payment for {$tenancy->room->room_label} (₱".number_format($amount, 2).")"
                    : "Late payment for {$tenancy->room->room_label} (₱".number_format($amount, 2).")",
                tenancyId: $tenancy->id,
                paymentId: $payment->id,
                createdBy: $landlord->id,
            );

            return $payment;
        });
    }
}