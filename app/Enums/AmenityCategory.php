<?php // app/Enums/AmenityCategory.php
namespace App\Enums;
enum AmenityCategory: string {
    case Connectivity = 'connectivity';
    case Utility      = 'utility';
    case Safety       = 'safety';
    case Lifestyle    = 'lifestyle';
}