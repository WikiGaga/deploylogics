<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// class PusherNotifyEvent implements ShouldBroadcast
class PusherNotifyEvent implements ShouldBroadcastNow
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
        Log::info('Event Instantiated', [
            'userId' => $userId,
            'message' => $message,
            'messageUrl' => $messageUrl,
        ]);
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
        Log::info('Broadcasting Data', [
            'message' => $this->message,
            'url' => $this->messageUrl,
        ]);
        return [
            'message' => $this->message,
            'messageUrl' => $this->messageUrl,
        ];
    }

    public function broadcastAs()
    {
        return 'PusherNotifyEvent';
    }
}
