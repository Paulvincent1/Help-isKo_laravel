<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ActiveDutyNotification extends Notification
{
    use Queueable;

    protected $duty;
    protected $user; // Include this to store the user receiving the notification

    public function __construct($duty, $user)
    {
        $this->duty = $duty;
        $this->user = $user; // Store the user in the constructor
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Duty Active!',
            'message' => 'The duty at ' . $this->duty->building . ' is now active.',
            'duty_id' => $this->duty->id,
            'user' => $this->user->name, // Include user info if needed
            'time' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Duty Active!',
            'message' => 'The duty at ' . $this->duty->building . ' is now active.',
            'duty_id' => $this->duty->id,
            'user' => $this->user->name,
            'time' => now(),
        ]);
    }
}
