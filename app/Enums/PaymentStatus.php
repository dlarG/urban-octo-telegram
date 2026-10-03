<?php // app/Enums/PaymentStatus.php
namespace App\Enums;
enum PaymentStatus: string { 
    case Pending = 'pending'; 
    case Paid = 'paid'; 
    case Late = 'late'; 
    case Failed = 'failed'; 

    public function label(): string {
        return match($this) {
            self::Pending => 'Pending',
            self::Paid    => 'Paid',
            self::Late    => 'Late',
            self::Failed  => 'Failed',
        };
    }
}