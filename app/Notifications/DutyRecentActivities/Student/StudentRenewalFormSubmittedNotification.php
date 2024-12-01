<?php

namespace App\Notifications\DutyRecentActivities\Student;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class StudentRenewalFormSubmittedNotification extends Notification
{
    use Queueable;

    protected $renewalForm;

    public function __construct($renewalForm)
    {
        $this->renewalForm = $renewalForm;
    }

    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Renewed',
            'description' => 'Renewal Form Submitted!',
            'message' => 'Your renewal form has been submitted successfully.',
            'renewal_form_id' => $this->renewalForm->id,
            'time' => now(),
        ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Renewed!',
            'description' => 'Renewal Form Submitted!',
            'message' => 'Your renewal form has been submitted successfully.',
            'renewal_form_id' => $this->renewalForm->id,
            'time' => now(),
        ];
    }
}
