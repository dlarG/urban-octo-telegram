<?php // app/Enums/PropertyStatus.php
namespace App\Enums;
enum PropertyStatus: string {
    case PendingReview = 'pending_review';
    case Active        = 'active';
    case Inactive      = 'inactive';
    case Suspended     = 'suspended';

    public function label(): string {
        return match($this) {
            self::PendingReview => 'Pending Review',
            self::Active        => 'Active',
            self::Inactive      => 'Inactive',
            self::Suspended     => 'Suspended',
        };
    }
}