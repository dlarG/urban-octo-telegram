<?php
namespace App\Notifications;

use App\Models\RentalApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewApplicationReceived extends Notification
{
    use Queueable;

    public function __construct(public RentalApplication $application) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'application.received',
            'title'   => 'New application',
            'body'    => $this->application->renter->name
                        .' applied to '.$this->application->room->room_label,
            'url'     => route('landlord.applications.show', $this->application),
            'app_id'  => $this->application->id,
        ];
    }
}