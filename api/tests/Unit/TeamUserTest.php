<?php

namespace Tests\Unit;

use App\Events\MultiChannelEvent;
use App\Events\PlayerDetachedFromTeamEvent;
use App\Events\TeamDestroyedEvent;
use App\Events\TeamUpdatedEvent;
use App\Http\Enums\TournamentUserState;
use App\Models\Fight;
use App\Models\Notification;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\Tournament;
use App\Models\User;
use Event;
use Tests\TestCase;

class TeamUserTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = TeamUser::factory()->create();
    }

    /* Relationships */

    public function testItHasTeam()
    {
        $this->assertInstanceOf(Team::class, $this->record->team);
    }

    public function testItHasUser()
    {
        $this->assertInstanceOf(User::class, $this->record->user);
    }
}
