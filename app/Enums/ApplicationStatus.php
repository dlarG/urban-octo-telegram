<?php // app/Enums/ApplicationStatus.php
namespace App\Enums;
enum ApplicationStatus: string {
    case Submitted = 'submitted';
    case Viewed    = 'viewed';
    case Accepted  = 'accepted';
    case Rejected  = 'rejected';
    case Withdrawn = 'withdrawn';

    public function label(): string {
        return match($this) {
            self::Submitted => 'Submitted',
            self::Viewed    => 'Viewed',
            self::Accepted  => 'Accepted',
            self::Rejected  => 'Rejected',
            self::Withdrawn => 'Withdrawn',
        };
    }

    public function blocksNewApplication(): bool {
        return in_array($this, [self::Submitted, self::Viewed, self::Accepted], true);
    }
}