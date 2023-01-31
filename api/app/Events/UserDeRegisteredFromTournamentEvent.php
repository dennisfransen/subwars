<?php

namespace App\Events;

use App\Models\Tournament;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDeRegisteredFromTournamentEvent extends MultiChannelEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id, $team_id;
    private $tournament_id;

    /**
     * Create a new event instance.
     *
     * @param Tournament $tournament
     * @param int $userId
     * @param int|null $teamId
     * @param int $channel
     * @param int|null $channelId
     */
    public function __construct(
        Tournament $tournament,
        int        $userId,
        int        $teamId = null,
        int        $channel = self::CHANNEL_TOURNAMENTS,
        int        $channelId = null
    )
    {
        parent::__construct($channel, $channelId);

        $this->tournament_id = $tournament->id;
        $this->user_id = $userId;
        $this->team_id = $teamId;
    }
}
