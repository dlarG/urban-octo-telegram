<?php // app/Enums/PropertyStatus.php
namespace App\Enums;
enum PropertyStatus: string {
    case PendingReview = 'pending_review';
    case Active        = 'active';
    case Inactive      = 'inactive';
    case Suspended     = 'suspended';
}