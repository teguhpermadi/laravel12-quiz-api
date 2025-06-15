<?php

namespace App\Events;

use App\Models\Teacher; // Pastikan ini model Teacher Anda
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTeacherAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $teacher; // Properti yang akan dibroadcast

    /**
     * Create a new event instance.
     */
    public function __construct(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Menyiarkan ke channel publik 'teachers'
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
        return 'teacher.added'; // Nama event yang akan didengarkan di frontend
    }
}