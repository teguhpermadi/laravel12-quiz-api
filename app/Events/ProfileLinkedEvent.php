<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProfileLinkedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userableId;
    public $userableType;

    /**
     * Create a new event instance.
     */
    public function __construct(string $userableId, string $userableType)
    {
        $this->userableId = $userableId;
        $this->userableType = $userableType;
        Log::info('DEBUG: ProfileLinked event INSTANTIATED.', ['userable_id' => $userableId, 'userable_type' => $userableType]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('DEBUG: ProfileLinked event BROADCASTING to channel "profiles".');
        
        return [
            new Channel('profiles'), // Event ini akan disiarkan ke channel 'profiles'
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'profile.linked'; // Nama event yang akan didengarkan di frontend
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'userable_id' => $this->userableId,
            'userable_type' => $this->userableType,
        ];
    }
}
