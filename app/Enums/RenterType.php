<?php // app/Enums/RenterType.php
namespace App\Enums;
enum RenterType: string {
    case Student = 'student';
    case Worker  = 'worker';
    case Tourist = 'tourist';
    case Other   = 'other';

    public function label(): string {
        return match($this) {
            self::Student => 'Student',
            self::Worker  => 'Worker',
            self::Tourist => 'Tourist',
            self::Other   => 'Other',
        };
    }
}