<?php

namespace App\Events;

use App\Models\Teacher;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeacherUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $teacher; // Property to hold the teacher data

    /**
     * Create a new event instance.
     */
    public function __construct(Teacher $teacher)
    {
        $this->teacher = $teacher; // Assign the teacher data to the property
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
     * Defaultnya adalah nama kelas event (misal: 'NewTeacherAdded').
     * Anda bisa mengubahnya jika ingin nama yang lebih pendek di frontend.
     */
    public function broadcastAs(): string
    {
        return 'teacher.updated'; // Nama event yang akan didengarkan di frontend 
    } 
}
