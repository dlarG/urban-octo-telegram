<?php
namespace App\Notifications;

use App\Models\Tenancy;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TenancyEnded extends Notification
{
    use Queueable;

    public function __construct(public Tenancy $tenancy) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'tenancy.ended',
            'title'   => 'Tenancy ended',
            'body'    => 'Your tenancy at '.$this->tenancy->room->boardingHouse->name
                         .' has ended. Leave a review?',
            'url'     => route('renter.tenancies'),
            'tenancy' => $this->tenancy->id,
        ];
    }
}