<?php

namespace App\Events;

use App\Models\Tournament;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerLockedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    private $tournament_id;

    /**
     * Create a new event instance.
     *
     * @param Tournament $tournament
     * @param int $userId
     */
    public function __construct(Tournament $tournament, int $userId)
    {
        $this->tournament_id = $tournament->id;
        $this->user_id = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel("App.Models.Tournament." . $this->tournament_id);
    }
}
