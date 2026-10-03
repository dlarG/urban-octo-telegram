<?php // app/Enums/TrustEventType.php
namespace App\Enums;
enum TrustEventType: string {
    case PaymentOnTime      = 'payment_on_time';
    case PaymentLate        = 'payment_late';
    case CheckoutCompliant  = 'checkout_compliant';
    case CheckoutViolation  = 'checkout_violation';
    case DisputeUpheld      = 'dispute_upheld';
    case AdminAdjustment    = 'admin_adjustment';

    public function defaultDelta(): float
    {
        return match($this) {
            self::PaymentOnTime     =>  2.00,
            self::PaymentLate       => -5.00,
            self::CheckoutCompliant =>  2.00,
            self::CheckoutViolation => -10.00,
            self::DisputeUpheld     =>  5.00,
            self::AdminAdjustment   =>  0.00,
        };
    }
}