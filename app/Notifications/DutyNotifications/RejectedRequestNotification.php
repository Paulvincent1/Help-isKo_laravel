<?php
namespace App\Notifications\DutyNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class RejectedRequestNotification extends Notification
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
            'title' => 'Request Rejected',
            'message' => 'Your request for duty ' . $this->duty->building . ' on ' . $this->duty->date . ' was rejected.',
            'duty_id' => $this->duty->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Request Rejected',
            'message' => 'Your request for duty ' . $this->duty->building . ' on ' . $this->duty->date . ' was rejected.',
            'duty_id' => $this->duty->id,
        ]);
    }
}
