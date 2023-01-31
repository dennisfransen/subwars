<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayersSwitchedEvent extends MultiChannelEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_ids;

    /**
     * PlayersSwitchedEvent constructor.
     * @param array $userIds
     * @param int $channelId
     * @param int $channel
     */
    public function __construct(
        array $userIds,
        int $channelId,
        int $channel
    )
    {
        parent::__construct($channel, $channelId);

        $this->user_ids = $userIds;
    }
}
