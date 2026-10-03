<?php // app/Enums/UserRole.php
namespace App\Enums;
enum UserRole: string {
    case Admin    = 'admin';
    case Landlord = 'landlord';
    case Renter   = 'renter';

    public function label(): string {
        return match($this) {
            self::Admin    => 'Admin',
            self::Landlord => 'Landlord',
            self::Renter   => 'Renter',
        };
    }
}