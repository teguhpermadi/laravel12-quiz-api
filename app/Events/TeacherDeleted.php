<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeacherDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The ID of the teacher that was deleted.
     *
     * @var int
     */
    public $teacherId;
    
    /**
     * Create a new event instance.
     */
    public function __construct(string $teacherId)
    {
        $this->teacherId = $teacherId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('teachers'),
        ];
    }

    /**
     * The event's broadcast name.
     * Defaultnya adalah nama kelas event (misal: 'TeacherDeleted').
     * Anda bisa mengubahnya jika ingin nama yang lebih pendek di frontend.
     */
    public function broadcastAs(): string
    {
        return 'teacher.deleted'; // Nama event yang akan didengarkan di frontend
    }
}
