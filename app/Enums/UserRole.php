<?php // app/Enums/UserRole.php
namespace App\Enums;
enum UserRole: string {
    case Admin    = 'admin';
    case Landlord = 'landlord';
    case Renter   = 'renter';
}