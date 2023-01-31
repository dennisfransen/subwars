<?php

namespace Tests\Feature;

use App\Events\TournamentUpdatedEvent;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentDetachFromTeamsTest extends ApiTestCase
{
    private $record, $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();

        $users = User::factory()->count(15)->create();
        $this->record->users()->attach($users->pluck("id"), [
            "state" => TournamentUserState::CHECKED_IN,
        ]);

        $teams = Team::factory()->count(3)->create([
            "tournament_id" => $this->record->id,
        ]);
        $teams[0]->users()->attach($this->record->users()->limit(5)->offset(0)->pluck("id"));
        $teams[1]->users()->attach($this->record->users()->limit(5)->offset(5)->pluck("id"));
        $teams[2]->users()->attach($this->record->users()->limit(5)->offset(10)->pluck("id"));

        $this->data = [];
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->detachFromTeams($this->record->creator);

        Event::assertDispatchedTimes(TournamentUpdatedEvent::class, 2);
    }

    public function testDetachesAllPlayersWithoutUserIds()
    {
        $this->detachFromTeams($this->record->creator);

        $this->assertCount(0, $this->record->teams);
        $this->assertCount(15, $this->record->reserve);
    }

    public function testDetachesAllPlayersButUserIdsOfFirstTeam()
    {
        $this->data["user_ids"] = $this->record->teams()->first()->users()->pluck("users.id");
        $this->detachFromTeams($this->record->creator);

        $this->assertCount(1, $this->record->teams);
        $this->assertCount(10, $this->record->reserve);
    }

    public function testDetachesAllPlayersButUserIdsOfSeparateTeams()
    {
        $this->data["user_ids"][] = $this->record->teams[0]->users()->first()->id;
        $this->data["user_ids"][] = $this->record->teams[1]->users()->first()->id;
        $this->detachFromTeams($this->record->creator);

        $this->record->refresh();
        $this->assertCount(2, $this->record->teams);
        $this->assertCOunt(13, $this->record->reserve);
    }

    public function testFailsOnUserIdsNotBeingArray()
    {
        $this->data["user_ids"] = "bad";
        $this->detachFromTeams($this->record->creator)->assertStatus(422);
    }

    public function testReturnsHttpOk()
    {
        $this->detachFromTeams($this->record->creator)->assertStatus(200);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->detachFromTeams($this->record->creator)
            ->assertStatus(404);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->detachFromTeams(null)
            ->assertStatus(401);
    }

    public function testConfirmedCantUpdateAny()
    {
        $this->detachFromTeams(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->record->coCasters()->attach($coCaster);

        $this->detachFromTeams($coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->detachFromTeams(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function detachFromTeams(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.detach_from_teams", $this->record), $this->data);
    }
}
