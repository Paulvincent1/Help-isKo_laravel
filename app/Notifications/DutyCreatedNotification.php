<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class DutyCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $duty;

    public function __construct($duty)
    {
        $this->duty = $duty;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; // Optional: add broadcast for real-time updates
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Duty Created!',
            'message' => 'You have successfully created a duty for ' . $this->duty->building . ' on ' . $this->duty->date,
            'duty_id' => $this->duty->id,
            'role' => 'employee',
            'time' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Duty Created!',
            'message' => 'You have successfully created a duty for ' . $this->duty->building . ' on ' . $this->duty->date,
            'duty_id' => $this->duty->id,
            'role' => 'employee',
            'time' => now(),
        ]);
    }
}
