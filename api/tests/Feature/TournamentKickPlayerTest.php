<?php

namespace Tests\Feature;

use App\Events\TournamentUpdatedEvent;
use App\Events\UserRegisteredToTournamentEvent;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentKickPlayerTest extends ApiTestCase
{
    private $record, $user, $registered, $checkedIn, $registeredKicked, $checkedInKicked;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
        $this->user = User::factory()->create([
            "type" => UserType::SUPERADMIN,
            "esportal_username" => "MrMotoX",
        ]);
        $this->registered = User::factory()->create();
        $this->checkedIn = User::factory()->create();
        $this->registeredKicked = User::factory()->create();
        $this->checkedInKicked = User::factory()->create();
        $this->record->users()->attach($this->registered, [
            "state" => TournamentUserState::REGISTERED,
        ]);
        $this->record->users()->attach($this->checkedIn, [
            "state" => TournamentUserState::CHECKED_IN,
        ]);
        $this->record->users()->attach($this->registeredKicked, [
            "state" => TournamentUserState::REGISTERED_KICKED,
        ]);
        $this->record->users()->attach($this->checkedInKicked, [
            "state" => TournamentUserState::CHECKED_IN_KICKED,
        ]);
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->kickPlayerFromTournament(["user_id" => $this->registered->id], $this->user);

        Event::assertDispatchedTimes(TournamentUpdatedEvent::class);
    }

    public function testPlayerIsKickedOutOfTheirTeam()
    {
        $team = Team::factory()->create([
            "tournament_id" => $this->record->id,
        ]);
        $team->users()->attach($this->checkedIn);

        $this->kickPlayerFromTournament(["user_id" => $this->checkedIn->id], $this->user);

        $this->assertCount(0, TeamUser::where("team_id", $team->id)->where("user_id", $this->checkedIn->id)->get());
    }

    public function testKickedPlayerGeneratesNotification()
    {
        $this->kickPlayerFromTournament(["user_id" => $this->registered->id], $this->user);

        $notification = $this->registered->notifications()->first();
        $this->assertEquals(Tournament::class, $notification->notifiable_type);
        $this->assertEquals($this->record->id, $notification->notifiable_id);
        $this->assertEquals("You were kicked from a tournament.", $notification->description);
    }

    public function testKicksRegistered()
    {
        $this->kickPlayerFromTournament(["user_id" => $this->registered->id], $this->user);

        $state = $this->record->users()->where("users.id", $this->registered->id)->first()->pivot->state;
        $this->assertEquals(TournamentUserState::REGISTERED_KICKED, $state);
    }

    public function testKicksCheckedIn()
    {
        $this->kickPlayerFromTournament(["user_id" => $this->checkedIn->id], $this->user);

        $state = $this->record->users()->where("users.id", $this->checkedIn->id)->first()->pivot->state;
        $this->assertEquals(TournamentUserState::CHECKED_IN_KICKED, $state);
    }

    public function testFailsOnBadUserId()
    {
        $this->kickPlayerAndAssertFail(["user_id" => null]);
        $this->kickPlayerAndAssertFail(["user_id" => User::all()->count() + 1]);
    }

    public function testReturnsHttpOk()
    {
        $this->kickPlayerFromTournament(["user_id" => $this->registered->id], $this->user)->assertStatus(200);
    }

    public function testOwnerCanKickPlayer()
    {
        $owner = User::factory()->create();
        $this->record->user_id = $owner->id;
        $this->record->save();

        $this->kickPlayerFromTournament(["user_id" => $this->registered->id], $owner)
            ->assertStatus(200);
    }

    public function testCoCasterCanKickPlayer()
    {
        $coCaster = User::factory()->create();
        $this->record->coCasters()->attach($coCaster->id);

        $this->kickPlayerFromTournament(["user_id" => $this->registered->id], $coCaster)
            ->assertStatus(200);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->kickPlayerFromTournament([], null)
            ->assertStatus(401);
    }

    public function testConfirmedCannotKickPlayer()
    {
        $this->kickPlayerFromTournament(["user_id" => $this->registered->id], User::factory()->create([
            "type" => UserType::STREAMER,
        ]))->assertStatus(403);
    }

    /**
     * @param array $data
     */
    private function kickPlayerAndAssertFail(array $data)
    {
        $this->kickPlayerFromTournament($data, $this->user)
            ->assertStatus(422);
    }

    /**
     * @param array $data
     * @param User|null $user
     * @return TestResponse
     */
    private function kickPlayerFromTournament(array $data, User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.kick_player", $this->record), $data);
    }
}
