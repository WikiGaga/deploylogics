<?php

namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;



use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
class PusherEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('my-channel');
    }

    public function broadcastAs()
    {
        return 'my-event';
    }
}

// class PusherEvent implements ShouldBroadcastNow
// {

//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public $message;

//     public function __construct($message)
//     {
//         $this->message = $message;
//     }

//     public function broadcastOn()
//     {
//         return new Channel('my-channel');
//     }

//     public function broadcastAs()
//     {
//         return 'my-event';
//     }

//     public function broadcastWith()
//     {
//         return ['message' => $this->message];
//     }
// }






// Pusher.logToConsole = true;
//         var pusher = new Pusher('cd64b71c5c904100352c', {
//         cluster: 'mt1',
//         // forceTLS: true,
//         //   enabledTransports: ['ws', 'wss', 'xhr_streaming', 'xhr_polling']
//         enabledTransports: ['ws', 'wss']
//         });

//         var channel = pusher.subscribe('my-channel');
//         channel.bind('my-event', function(data) {
//             // if(data.message =='unpaid'){
//                 window.location.reload();
//             // }
//             console.log(data,data.message);
            
//         });

// event(new PusherEvent('unpaid'));