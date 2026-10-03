<?php // app/Enums/ApplicationStatus.php
namespace App\Enums;
enum ApplicationStatus: string {
    case Submitted = 'submitted';
    case Viewed    = 'viewed';
    case Accepted  = 'accepted';
    case Rejected  = 'rejected';
    case Withdrawn = 'withdrawn';

    public function blocksNewApplication(): bool {
        return in_array($this, [self::Submitted, self::Viewed, self::Accepted], true);
    }
}