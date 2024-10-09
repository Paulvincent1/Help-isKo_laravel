<?php

namespace App\Notifications\DutyNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UnfinishedDutyNotification extends Notification
{
    use Queueable;

    protected $duty;

    public function __construct($duty)
    {
        $this->duty = $duty;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Unfinished Duty!',
            'message' => 'The duty scheduled for ' . $this->duty->date . ' was not completed.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Unfinished Duty!',
            'message' => 'The duty scheduled for ' . $this->duty->date . ' was not completed.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ]);
    }
}
