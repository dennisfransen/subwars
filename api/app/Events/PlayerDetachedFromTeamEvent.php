<?php

namespace App\Events;

use App\Http\Resources\RegisteredResource;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerDetachedFromTeamEvent extends MultiChannelEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id, $reserve, $team;

    /**
     * PlayerDetachedFromTeamEvent constructor.
     * @param Team $team
     * @param int $userId
     * @param int $channel
     * @param int|null $channelId
     */
    public function __construct(Team $team, int $userId, int $channel = self::CHANNEL_TOURNAMENTS, int $channelId = null)
    {
        parent::__construct($channel, $channelId);

        $this->id = $team->id;
        $this->reserve = RegisteredResource::collection(Tournament::find($team->tournament_id)->reserve);
        $this->team = new TeamResource($team->load(["users"]));
    }
}
