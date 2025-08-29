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

class NewGroupMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($message)
    {
        //
        $this->message = $message->load(['user']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('group.' . $this->message->study_group_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            'sender_id'     => $this->message->user_id,
            'id'            => $this->message->id,
            'username'      => $this->message->user->username,
            'message'       => $this->message->message,
            'file_url'      => $this->message->file_path ? asset('storage/' . $this->message->file_path) : null,
            'file_name'     => $this->message->file_path ? $this->message->file_name : null,
            'timestamp'     => $this->message->created_at->format('H:i a'),
        ];
    }
}
