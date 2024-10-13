<?php
namespace App\Notifications\DutyNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage; // Add this import

class AcceptedDutyNotification extends Notification
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
            'title' => 'Duty Accepted!',
            'message' => 'Your request for duty at ' . $this->duty->building . ' has been accepted.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Duty Accepted!',
            'message' => 'Your request for duty at ' . $this->duty->building . ' has been accepted.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ]);
    }
}
