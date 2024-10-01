<?php

namespace App\Notifications\DutyRecentActivities;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class DutyRemovedNotification extends Notification
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
            'title' => 'Deleted',
            'description' => 'You removed a duty!',
            'message' => 'A duty scheduled for ' . $formattedDate . ' has been removed by you.',
            'duty_id' => $this->duty->id,
            'role' => 'employee',
            'time' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        $formattedDate = Carbon::parse($this->duty->date)->format('F j, Y');

        return new BroadcastMessage([
            'title' => 'Deleted',
            'description' => 'You removed a duty!',
            'message' => 'A duty scheduled for ' . $formattedDate . ' has been removed by you.',
            'duty_id' => $this->duty->id,
            'role' => 'employee',
            'time' => now(),
        ]);
    }
}
