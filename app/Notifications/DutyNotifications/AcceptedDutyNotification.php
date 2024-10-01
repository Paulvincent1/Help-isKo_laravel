<?php
namespace App\Notifications;

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
            'message' => 'Your request for duty ' . $this->duty->building . ' has been accepted.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'Your request for duty ' . $this->duty->building . ' has been accepted.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ]);
    }
}
