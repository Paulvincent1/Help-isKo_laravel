<?php

namespace App\Notifications\DutyNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class RenewalStatusNotification extends Notification
{
    use Queueable;

    protected $renewalForm;
    protected $status;

    public function __construct($renewalForm, $status)
    {
        $this->renewalForm = $renewalForm;
        $this->status = $status;
    }

    // Define how the notification will be delivered
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    // Format the notification for the database
    public function toDatabase($notifiable)
    {
        $message = $this->status === 'approved'
            ? 'Your renewal request has been approved!'
            : 'Your renewal request has been rejected.';

        return [
            'title' => $this->status === 'approved' ? 'Renewal Approved!' : 'Renewal Rejected!',
            'message' => $message,
            'renewal_id' => $this->renewalForm->id,
            'time' => now(),
        ];
    }

    // Format the notification for broadcasting
    public function toBroadcast($notifiable)
    {
        $message = $this->status === 'approved'
            ? 'Your renewal request has been approved!'
            : 'Your renewal request has been rejected.';

        return new BroadcastMessage([
            'title' => $this->status === 'approved' ? 'Renewal Approved!' : 'Renewal Rejected!',
            'message' => $message,
            'renewal_id' => $this->renewalForm->id,
            'time' => now(),
        ]);
    }
}
