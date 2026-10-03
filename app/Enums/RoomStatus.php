<?php // app/Enums/RoomStatus.php
namespace App\Enums;
enum RoomStatus: string {
    case Available   = 'available';
    case Full        = 'full';
    case Maintenance = 'maintenance';
    case Delisted    = 'delisted';

    public function label(): string {
        return match($this) {
            self::Available   => 'Available',
            self::Full        => 'Full',
            self::Maintenance => 'Maintenance',
            self::Delisted    => 'Delisted',
        };
    }
}