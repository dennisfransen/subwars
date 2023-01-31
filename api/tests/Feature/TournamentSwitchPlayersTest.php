<?php

namespace Tests\Feature;

use App\Events\PlayersSwitchedEvent;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentSwitchPlayersTest extends ApiTestCase
{
    private $records, $data, $tournament;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tournament = Tournament::factory()->create([
            "user_id" => User::factory()->create(["type" => UserType::STREAMER]),
        ]);

        $this->records = Team::factory()->count(2)->create([
            "tournament_id" => $this->tournament->id,
        ]);
        $this->records[0]->users()->attach(User::factory()->count(4)->create()->pluck("id"));
        $this->records[1]->users()->attach(User::factory()->count(4)->create()->pluck("id"));

        $this->tournament->users()->attach(User::all()->pluck("id"), [
            "state" => TournamentUserState::CHECKED_IN,
        ]);

        $this->data["user_ids"][] = $this->records[0]->users[0]->id;
        $this->data["user_ids"][] = $this->records[1]->users[0]->id;
    }

    public function testReturnsHttpOk()
    {
        $this->switchPlayers($this->tournament->creator)->assertStatus(200);
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->switchPlayers($this->tournament->creator);

        Event::assertDispatchedTimes(PlayersSwitchedEvent::class, 3);
    }

    public function testPlayersInTeamsGetsSwitched()
    {
        $this->switchPlayers($this->tournament->creator);

        $this->assertEquals($this->records[1]->id, User::find($this->data["user_ids"][0])->teams[0]->id);
        $this->assertEquals($this->records[0]->id, User::find($this->data["user_ids"][1])->teams[0]->id);
    }

    public function testPlayersInTeamAndReserveGetsSwitched()
    {
        $reservePlayer = User::factory()->create();
        $this->tournament->users()->attach($reservePlayer->id, [
            "state" => TournamentUserState::CHECKED_IN,
        ]);

        $this->data["user_ids"][1] = $reservePlayer->id;

        $this->switchPlayers($this->tournament->creator);

        $this->assertEquals($this->records[0]->id, User::find($this->data["user_ids"][1])->teams[0]->id);
        $this->assertCount(0, User::find($this->data["user_ids"][0])->teams);
    }

    public function testFailsOnBothPlayersNotInTeams()
    {
        $reservePlayers = User::factory()->count(2)->create();
        $this->tournament->users()->attach($reservePlayers->pluck("id"), [
            "state" => TournamentUserState::CHECKED_IN,
        ]);

        $this->data["user_ids"] = $reservePlayers->pluck("id");

        $this->switchPlayers($this->tournament->creator)->assertStatus(422);
    }

    public function testFailsOnFirstPlayerNotCheckedIn()
    {
        $this->tournament->users()->updateExistingPivot($this->data["user_ids"][0], [
            "state" => TournamentUserState::REGISTERED,
        ]);

        $this->switchPlayers($this->tournament->creator)->assertStatus(422);
    }

    public function testFailsOnSecondPlayerNotCheckedIn()
    {
        $this->tournament->users()->updateExistingPivot($this->data["user_ids"][1], [
            "state" => TournamentUserState::REGISTERED,
        ]);

        $this->switchPlayers($this->tournament->creator)->assertStatus(422);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->tournament->delete();

        $this->switchPlayers($this->tournament->creator)
            ->assertStatus(404);
    }

    public function testFailsOnFirstUserNotFound()
    {
        $this->data["user_ids"][0] = User::count() + 1;

        $this->switchPlayers($this->tournament->creator)
            ->assertStatus(422);
    }

    public function testFailsOnSecondUserNotFound()
    {
        $this->data["user_ids"][1] = User::count() + 1;

        $this->switchPlayers($this->tournament->creator)
            ->assertStatus(422);
    }

    public function testFailsOnMissingUserIds()
    {
        unset($this->data["user_ids"]);

        $this->switchPlayers($this->tournament->creator)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->switchPlayers(null)
            ->assertStatus(401);
    }

    public function testMemberReturnsUnauthenticated()
    {
        $this->switchPlayers(User::factory()->create(["type" => UserType::MEMBER]))
            ->assertStatus(403);
    }

    public function testConfirmedCantUpdateOther()
    {
        $this->switchPlayers(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->tournament->coCasters()->attach($coCaster);

        $this->switchPlayers($coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->switchPlayers(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function switchPlayers(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.switch_players", $this->tournament), $this->data);
    }
}
