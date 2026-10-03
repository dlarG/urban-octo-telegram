<?php // app/Enums/RenterType.php
namespace App\Enums;
enum RenterType: string {
    case Student = 'student';
    case Worker  = 'worker';
    case Tourist = 'tourist';
    case Other   = 'other';
}