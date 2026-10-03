<?php // app/Enums/RoomStatus.php
namespace App\Enums;
enum RoomStatus: string {
    case Available   = 'available';
    case Full        = 'full';
    case Maintenance = 'maintenance';
    case Delisted    = 'delisted';
}