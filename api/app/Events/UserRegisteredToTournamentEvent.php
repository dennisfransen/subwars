<?php

namespace App\Events;

use App\Http\Resources\UserSimpleResource;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredToTournamentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    private $tournament_id;

    /**
     * Create a new event instance.
     *
     * @param Tournament $tournament
     * @param User $user
     */
    public function __construct(Tournament $tournament, User $user)
    {
        $this->tournament_id = $tournament->id;
        $this->user = new UserSimpleResource($user);
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
