<?php
namespace App\Notifications;

use App\Models\RentalApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationDecisionMade extends Notification
{
    use Queueable;

    public function __construct(public RentalApplication $application) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $accepted = $this->application->status->value === 'accepted';
        return [
            'type'   => 'application.' . ($accepted ? 'accepted' : 'rejected'),
            'title'  => $accepted ? 'Application accepted' : 'Application rejected',
            'body'   => ($accepted ? 'You were accepted for ' : 'You were not selected for ')
                        . $this->application->room->room_label
                        . ' at ' . $this->application->room->boardingHouse->name,
            'url'    => route('renter.applications'),
            'app_id' => $this->application->id,
        ];
    }
}