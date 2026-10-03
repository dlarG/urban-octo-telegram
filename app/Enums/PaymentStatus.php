<?php // app/Enums/PaymentStatus.php
namespace App\Enums;
enum PaymentStatus: string { case Pending = 'pending'; case Paid = 'paid'; case Late = 'late'; case Failed = 'failed'; }