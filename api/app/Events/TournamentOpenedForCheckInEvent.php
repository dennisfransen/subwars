<?php

namespace App\Events;

use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TournamentOpenedForCheckInEvent extends MultiChannelEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tournament;

    /**
     * Create a new event instance.
     *
     * @param Tournament $tournament
     * @param int $channel
     * @param int|null $id
     */
    public function __construct(Tournament $tournament, int $channel = self::CHANNEL_TOURNAMENTS, int $id = null)
    {
        parent::__construct($channel, $id);

        $this->tournament = new TournamentResource($tournament->load(["teams", "reserve", "coCasters"]));
    }
}
