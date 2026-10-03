<?php // app/Enums/LandlordApprovalStatus.php
namespace App\Enums;
enum LandlordApprovalStatus: string {
    case Pending  = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string {
        return match($this) {
            self::Pending  => 'Pending',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
        };
    }
}