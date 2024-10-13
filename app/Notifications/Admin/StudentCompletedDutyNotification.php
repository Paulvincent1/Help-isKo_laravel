<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentCompletedDutyNotification extends Notification
{
    use Queueable;
    public $duty;

    /**
     * Create a new notification instance.
     */
    public function __construct($duty)
    {
        $this->duty = $duty;
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

    public function toDatabase(object $notifiable){
        return [
            'building' => $this->duty->building,
            'date' => $this->duty->date,
            'start_time' => $this->duty->start_time,
            'end_time' => $this->duty->end_time,
            'duration' => $this->duty->duration
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
