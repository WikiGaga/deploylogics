<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PusherNotifyEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $message;
    public $messageUrl;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId, $message, $messageUrl)
    {
        $this->userId = $userId;
        $this->message = $message;
        $this->messageUrl = $messageUrl;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // dd('App.User.' . $this->userId);
        return new PrivateChannel('App.User.' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'url' => $this->messageUrl,
        ];
    }

    public function broadcastAs()
    {
        return 'PusherNotifyEvent';
    }
}
