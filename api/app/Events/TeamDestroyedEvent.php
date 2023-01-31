<?php

namespace App\Events;

use App\Models\Team;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamDestroyedEvent extends MultiChannelEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id, $tournament_id;

    /**
     * TeamDestroyedEvent constructor.
     * @param Team $team
     * @param int $channel
     * @param int|null $channelId
     */
    public function __construct(Team $team, int $channel = self::CHANNEL_TOURNAMENTS, int $channelId = null)
    {
        parent::__construct($channel, $channelId);

        $this->id = $team->id;
        $this->tournament_id = $team->tournament_id;
    }
}
