<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $taskId;
    public $columnId;
    public $teamId;

    public function __construct($taskId, $columnId, $teamId)
    {
        $this->taskId = $taskId;
        $this->columnId = $columnId;
        $this->teamId = $teamId;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('team.' . $this->teamId);
    }
}