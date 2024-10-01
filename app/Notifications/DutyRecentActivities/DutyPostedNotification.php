<?php

namespace App\Notifications\DutyRecentActivities;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class DutyPostedNotification extends Notification
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
        $formattedDate = Carbon::parse($this->duty->date)->format('F j, Y');
        
        return [
            'title' => 'Created',  // Title for posted duty
            'description' => 'You posted a duty!',  // Description of action
            'message' => 'A new duty has been posted by you. Check for more details.',  // Message for the notification
            'duty_id' => $this->duty->id,
            'role' => 'employee',
            'time' => now(),
            'duty_info' => [
                'building' => $this->duty->building,
                'date' => $this->duty->date,
                'start_time' => $this->duty->start_time,
                'end_time' => $this->duty->end_time,
                'message' => $this->duty->message,
                'max_scholars' => $this->duty->max_scholars,
                'current_scholars' => $this->duty->current_scholars,
                'status' => $this->duty->duty_status,
            ],
        ];
    }

    public function toBroadcast($notifiable)
    {
        $formattedDate = Carbon::parse($this->duty->date)->format('F j, Y');

        return new BroadcastMessage([
            'title' => 'Created',  
            'description' => 'You posted a duty!', 
            'message' => 'A new duty has been posted by you. Check for more details.',
            'duty_id' => $this->duty->id,
            'role' => 'employee',
            'time' => now(),
            'duty_info' => [
                'building' => $this->duty->building,
                'date' => $this->duty->date,
                'start_time' => $this->duty->start_time,
                'end_time' => $this->duty->end_time,
                'message' => $this->duty->message,
                'max_scholars' => $this->duty->max_scholars,
                'current_scholars' => $this->duty->current_scholars,
                'status' => $this->duty->duty_status,
            ],
        ]);
    }
}
