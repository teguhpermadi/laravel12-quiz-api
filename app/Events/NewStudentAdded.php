<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewStudentAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $student; // Properti yang akan dibroadcast

    /**
     * Create a new event instance.
     */
    public function __construct($student)
    {
        $this->student = $student;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('students'),
        ];
    }

    /**
     * The event's broadcast name.
     * Defaultnya adalah nama kelas event (misal: 'NewStudentAdded').
     * Anda bisa mengubahnya jika ingin nama yang lebih pendek di frontend.
     */
    public function broadcastAs(): string
    {
        return 'student.added'; // Nama event yang akan didengarkan di frontend
    }
}
