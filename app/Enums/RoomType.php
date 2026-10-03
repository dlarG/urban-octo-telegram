<?php // app/Enums/RoomType.php
namespace App\Enums;
enum RoomType: string { 
    case Private = 'private'; 
    case Shared = 'shared'; 

    public function label(): string {
        return match($this) {
            self::Private => 'Private',
            self::Shared  => 'Shared',
        };
    }

}