<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CancelledDutyNotification extends Notification
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
            'title' => 'Duty Cancelled!',
            'message' => 'The duty at ' . $this->duty->building . ' on ' . $this->duty->date . ' has been cancelled.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Duty Cancelled!',
            'message' => 'The duty at ' . $this->duty->building . ' on ' . $this->duty->date . ' has been cancelled.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ]);
    }
}

