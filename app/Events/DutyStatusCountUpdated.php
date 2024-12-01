<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DutyStatusCountUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $activeDutiesCount;
    public $ongoingDutiesCount;
    public $completedDutiesCount;
    public $totalDutiesCount;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $activeDutiesCount, $ongoingDutiesCount, $completedDutiesCount, $totalDutiesCount)
    {
        $this->userId = $userId;
        $this->activeDutiesCount = $activeDutiesCount;
        $this->ongoingDutiesCount = $ongoingDutiesCount;
        $this->completedDutiesCount = $completedDutiesCount;
        $this->totalDutiesCount = $totalDutiesCount;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('student.' . $this->userId);
    }
}
