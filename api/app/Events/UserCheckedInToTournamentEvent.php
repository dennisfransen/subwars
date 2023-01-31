<?php

namespace App\Events;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCheckedInToTournamentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    private $tournament_id;

    /**
     * Create a new event instance.
     *
     * @param Tournament $tournament
     * @param User $user
     */
    public function __construct(Tournament $tournament, User $user)
    {
        $this->user_id = $user->id;
        $this->tournament_id = $tournament->id;
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
