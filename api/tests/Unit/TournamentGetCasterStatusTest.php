<?php

namespace Tests\Unit;

use App\Events\PlayerMovedEvent;
use App\Events\TeamsScrambledEvent;
use App\Events\TournamentUpdatedEvent;
use App\Events\UserDeRegisteredFromTournamentEvent;
use App\Http\Enums\CasterRole;
use App\Http\Enums\CasterState;
use App\Http\Enums\TournamentUserState;
use App\Models\Bracket;
use App\Models\Fight;
use App\Models\Notification;
use App\Models\Sponsor;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;
use Event;
use Tests\TestCase;

class TournamentGetCasterStatusTest extends TestCase
{
    private $record, $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
        $this->user = User::factory()->create();

        $this->record->user_id = $this->user->id;
        $this->record->save();

        $this->record->coCasters()->attach($this->user, [
            "role" => CasterRole::MAIN_CASTER,
        ]);
        $this->record->coCasters()->attach($this->user, [
            "role" => CasterRole::CO_CASTER,
        ]);

        $this->record->refresh();
    }

    public function testItGetsMainCaster(): void
    {
        $this->assertEquals(CasterState::MAIN_CASTER, $this->record->getCasterStatus($this->user->id));
    }

    public function testItGetsCoCaster(): void
    {
        $this->record->coCasters()->wherePivot("role", CasterRole::MAIN_CASTER)->detach();

        $this->assertEquals(CasterState::CO_CASTER, $this->record->getCasterStatus($this->user->id));
    }

    public function testItGetsOwner(): void
    {
        $this->record->coCasters()->detach();

        $this->assertEquals(CasterState::OWNER, $this->record->getCasterStatus($this->user->id));
    }

    public function testItGetsNull(): void
    {
        $this->record->user_id = User::factory()->create()->id;
        $this->record->coCasters()->detach();
        $this->record->save();

        $this->assertEquals(0, $this->record->getCasterStatus($this->user->id));
    }
}
