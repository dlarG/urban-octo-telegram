<?php // app/Enums/TenancyStatus.php
namespace App\Enums;
enum TenancyStatus: string {
    case Active    = 'active';
    case Completed = 'completed';
    case Terminated= 'terminated';

    public function label(): string {
        return match($this) {
            self::Active     => 'Active',
            self::Completed  => 'Completed',
            self::Terminated => 'Terminated',
        };
    }
}