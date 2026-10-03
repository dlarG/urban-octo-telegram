<?php // app/Enums/TrustEventType.php
namespace App\Enums;
enum TrustEventType: string {
    case PaymentOnTime      = 'payment_on_time';
    case PaymentLate        = 'payment_late';
    case CheckoutCompliant  = 'checkout_compliant';
    case CheckoutViolation  = 'checkout_violation';
    case DisputeUpheld      = 'dispute_upheld';
    case AdminAdjustment    = 'admin_adjustment';
}