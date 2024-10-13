<?php

namespace App\Notifications\DutyNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CancelledDutyNotification extends Notification
{
    use Queueable;

    protected $duty;
    protected $user;

    public function __construct($duty, $user)
    {
        $this->duty = $duty;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Duty Cancelled!',
            'message' => 'The duty at ' . $this->duty->building . ' has been cancelled by you.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Duty Cancelled!',
            'message' => 'The duty at ' . $this->duty->building . ' has been cancelled by you.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ]);
    }
}
