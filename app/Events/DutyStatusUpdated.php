<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Duty;

class DutyStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $duty;

    public function __construct(Duty $duty)
    {
        $this->duty = $duty;
    }

    public function broadcastOn()
    {
        return new Channel('duty-status');
    }

    public function broadcastWith()
    {
        return [
            'duty_id' => $this->duty->id,
            'duty_status' => $this->duty->duty_status,
            'building' => $this->duty->building,
            'start_time' => $this->duty->start_time,
            'end_time' => $this->duty->end_time,
        ];
    }
}