<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CompletedDutyNotification extends Notification
{
    use Queueable;

    protected $duty;
    protected $user;

    public function __construct($duty, $user)
    {
        $this->duty = $duty;
        $this->user = $user; // This will help tailor the message based on the role
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Duty Completed!',
            'message' => $this->getMessage(), // Custom message for student or employee
            'duty_id' => $this->duty->id,
            'time' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Duty Completed!',
            'message' => $this->getMessage(),
            'duty_id' => $this->duty->id,
            'time' => now(),
        ]);
    }

    private function getMessage()
    {
        // Differentiate message based on role
        if ($this->user->role === 'student') {
            return 'You completed a duty at ' . $this->duty->building . ' on ' . $this->duty->date;
        } elseif ($this->user->role === 'employee') {
            return 'Student ' . $this->user->name . ' completed the duty at ' . $this->duty->building . ' on ' . $this->duty->date;
        }
    }
}
