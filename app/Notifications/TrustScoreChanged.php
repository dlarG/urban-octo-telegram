<?php
namespace App\Notifications;

use App\Models\TrustScoreEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrustScoreChanged extends Notification
{
    use Queueable;

    public function __construct(public TrustScoreEvent $event) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $positive = $this->event->delta >= 0;
        return [
            'type'    => 'trust.' . ($positive ? 'up' : 'down'),
            'title'   => $positive ? 'Trust score improved' : 'Trust score changed',
            'body'    => ($positive ? '+' : '') . number_format($this->event->delta, 2)
                         . ' — ' . $this->event->reason,
            'url'     => route('renter.trust'),
            'event'   => $this->event->id,
        ];
    }
}