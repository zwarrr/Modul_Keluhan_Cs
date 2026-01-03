<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sesiId;
    public $messageIds;

    public function __construct($sesiId, $messageIds = [])
    {
        $this->sesiId = $sesiId;
        $this->messageIds = $messageIds;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->sesiId);
    }

    public function broadcastAs()
    {
        return 'read';
    }
}
