<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentDutyCancelled extends Notification
{
    use Queueable;
    public $duty;
    public $userEmployee;

    /**
     * Create a new notification instance.
     */
    public function __construct($duty, $userEmployee)
    {
        $this->duty = $duty;
        $this->userEmployee = $userEmployee;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(){
        return [
            'title' => 'Duty Cancelled!',
            'message' => 'The duty at ' . $this->duty->building . ' has been cancelled by ' . $this->userEmployee->name . '.',
            'duty_id' => $this->duty->id,
            'time' => now(),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
