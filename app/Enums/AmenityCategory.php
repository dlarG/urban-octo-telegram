<?php // app/Enums/AmenityCategory.php
namespace App\Enums;
enum AmenityCategory: string {
    case Connectivity = 'connectivity';
    case Utility      = 'utility';
    case Safety       = 'safety';
    case Lifestyle    = 'lifestyle';

    public function label(): string {
        return match($this) {
            self::Connectivity => 'Connectivity',
            self::Utility      => 'Utility',
            self::Safety       => 'Safety',
            self::Lifestyle    => 'Lifestyle',
        };
    }
}