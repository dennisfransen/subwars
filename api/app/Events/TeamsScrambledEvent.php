<?php

namespace App\Events;

use App\Http\Resources\RegisteredResource;
use App\Http\Resources\TeamResource;
use App\Models\Tournament;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamsScrambledEvent extends MultiChannelEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reserve, $teams;

    /**
     * TeamsScrambledEvent constructor.
     * @param Tournament $tournament
     * @param int $channelId
     */
    public function __construct(Tournament $tournament, int $channelId)
    {
        parent::__construct(MultiChannelEvent::CHANNEL_TOURNAMENT, $channelId);

        $this->reserve = RegisteredResource::collection($tournament->reserve);
        $this->teams = TeamResource::collection($tournament->teams);
    }
}
