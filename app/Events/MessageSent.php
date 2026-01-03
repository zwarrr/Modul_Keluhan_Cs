<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sesiId;
    public $senderId;
    public $senderName;
    public $senderRole;
    public $filePath;
    public $fileType;
    public $time;
    public $status;
    public $messageId;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $sesiId, $senderId, $senderName, $senderRole, $filePath = null, $fileType = null, $time = null, $status = 'sent', $messageId = null)
    {
        $this->message = $message;
        $this->sesiId = $sesiId;
        $this->senderId = $senderId;
        $this->senderName = $senderName;
        $this->senderRole = $senderRole;
        $this->filePath = $filePath;
        $this->fileType = $fileType;
        $this->time = $time ?: now()->format('H:i');
        $this->status = $status;
        $this->messageId = $messageId;
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
        return 'message';
    }
}
