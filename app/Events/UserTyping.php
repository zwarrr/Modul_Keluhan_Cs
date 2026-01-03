<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $userName;
    public $userRole;
    public $sesiId;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $userName, $userRole, $sesiId)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userRole = $userRole;
        $this->sesiId = $sesiId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('chat.' . $this->sesiId);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'typing';
    }
}
