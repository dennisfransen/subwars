<?php

namespace App\Events;

use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamUpdatedEvent extends MultiChannelEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $team;

    /**
     * TeamDestroyedEvent constructor.
     * @param Team $team
     */
    public function __construct(Team $team)
    {
        parent::__construct(MultiChannelEvent::CHANNEL_TOURNAMENT, $team->tournament_id);

        $this->team = new TeamResource($team);
        $this->team->with = ["tournament_id" => $team->tournament_id];
    }
}
