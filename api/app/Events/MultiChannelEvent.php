<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class MultiChannelEvent implements ShouldBroadcast
{
    const CHANNEL_TOURNAMENTS = 1;
    const CHANNEL_TOURNAMENT = 2;
    const CHANNEL_USER = 3;

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channel, $channel_id;

    /**
     * Create a new event instance.
     *
     * @param int $channel
     * @param int|null $channelId
     */
    public function __construct(int $channel = self::CHANNEL_TOURNAMENTS, int $channelId = null)
    {
        $this->channel = $channel;
        $this->channel_id = $channelId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return $this->getChannel();
    }

    /**
     * @return Channel
     */
    private function getChannel(): Channel
    {
        switch ($this->channel) {
            case self::CHANNEL_TOURNAMENTS:
                return new Channel("App.Models.Tournament");
            case self::CHANNEL_TOURNAMENT:
                return new Channel("App.Models.Tournament." . $this->channel_id);
            default:
                return new Channel("App.Models.User." . $this->channel_id);
        }
    }
}
