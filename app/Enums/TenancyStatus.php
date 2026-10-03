<?php // app/Enums/TenancyStatus.php
namespace App\Enums;
enum TenancyStatus: string {
    case Active    = 'active';
    case Completed = 'completed';
    case Terminated= 'terminated';
}