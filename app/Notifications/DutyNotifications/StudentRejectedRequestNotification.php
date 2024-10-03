<?php

namespace App\Notifications\DutyNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Duty;

class StudentRejectedRequestNotification extends Notification
{
    use Queueable;

    protected $duty;

    /**
     * Create a new notification instance.
     *
     * @param Duty $duty
     */
    public function __construct(Duty $duty)
    {
        $this->duty = $duty;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Store the notification in the database.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Request Rejected',
            'message' => $this->getMessage(),
            'duty_id' => $this->duty->id,
            'duty_date' => $this->duty->date,
            'start_time' => $this->duty->start_time,
            'end_time' => $this->duty->end_time,
            'building' => $this->duty->building,
            'time' => now(),
        ];
    }

    /**
     * Broadcast the notification.
     *
     * @param mixed $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Request Rejected',
            'message' => $this->getMessage(),
            'duty_id' => $this->duty->id,
            'duty_date' => $this->duty->date,
            'start_time' => $this->duty->start_time,
            'end_time' => $this->duty->end_time,
            'building' => $this->duty->building,
            'time' => now(),
        ]);
    }

    /**
     * Generate the notification message.
     *
     * @return string
     */
    private function getMessage()
    {
        return 'Your duty request for "' . $this->duty->message . '" has been rejected because the max scholars limit has been reached.';
    }
}
