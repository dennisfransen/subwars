<?php

namespace App\Events;

use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TournamentOpenedForRegistrationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tournament;

    /**
     * Create a new event instance.
     *
     * @param Tournament $tournament
     */
    public function __construct(Tournament $tournament)
    {
        $this->tournament = new TournamentResource($tournament->load(["teams", "reserve", "coCasters"]));
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new Channel("App.Models.Tournament." . $this->tournament->id);
    }
}
