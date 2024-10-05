<?php

namespace App\Notifications\DutyRecentActivities\Student;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class StudentDutyRequestedNotification extends Notification
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
            'title' => 'Requested',
            'description' => 'You requested a duty!',
            'message' => 'You requested to join a duty scheduled for ' . $formattedDate . '.',
            'duty_id' => $this->duty->id,
            'role' => 'student',
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
            'title' => 'Requested',
            'description' => 'You requested a duty!',
            'message' => 'You requested to join a duty scheduled for ' . $formattedDate . '.',
            'duty_id' => $this->duty->id,
            'role' => 'student',
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
