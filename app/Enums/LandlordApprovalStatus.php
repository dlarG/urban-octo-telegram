<?php // app/Enums/LandlordApprovalStatus.php
namespace App\Enums;
enum LandlordApprovalStatus: string {
    case Pending  = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}