<?php

namespace Tests\Feature;

use App\Events\TournamentUpdatedEvent;
use App\Events\UserCheckedInToTournamentEvent;
use App\Events\UserRegisteredToTournamentEvent;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentCheckInTest extends ApiTestCase
{
    private $record, $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create([
            "registration_open_at" => Carbon::now()->subDays(2),
            "check_in_open_at" => Carbon::now()->subDays(2),
        ]);
        $this->user = User::factory()->create([
            "type" => UserType::SUPERADMIN,
            "esportal_username" => "MrMotoX",
        ]);

        $this->record->users()->attach($this->user, [
            "state" => TournamentUserState::REGISTERED,
        ]);
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->checkInToTournament([], $this->user);

        Event::assertDispatchedTimes(UserCheckedInToTournamentEvent::class, 1);
        Event::assertDispatchedTimes(TournamentUpdatedEvent::class, 1);
    }

    public function testChecksInLoggedInUser()
    {
        $this->checkInToTournament([], $this->user);

        $this->assertEquals($this->user->id, $this->record->users()->first()->id);
        $this->assertEquals(TournamentUserState::CHECKED_IN, $this->record->users()->first()->pivot->state);
    }

    public function testChecksInUserByRequest()
    {
        User::find(1)->update(["esportal_username" => "Poppy"]);
        $this->record->users()->attach(User::find(1), [
            "state" => TournamentUserState::REGISTERED,
        ]);

        $this->checkInToTournament(["user_id" => 1], $this->user);

        $this->assertEquals(1, $this->record->users()->first()->id);
        $this->assertEquals(
            TournamentUserState::CHECKED_IN,
            $this->record->users()->where("id", 1)->first()->pivot->state
        );
    }

    public function testReturnsHttpOk()
    {
        $this->checkInToTournament([], $this->user)->assertStatus(200);
    }

    public function testUpdatesUserEloFromEsportal()
    {
        $this->user->esportal_elo = 0;
        $this->user->save();

        $this->checkInToTournament([], $this->user);
        $this->user->refresh();

        $this->assertEquals(1200, $this->user->esportal_elo);
    }

    public function testFailsOnBadEsportalUsernameNull()
    {
        $this->user->esportal_username = null;
        $this->user->save();

        $this->checkInToTournament([], $this->user)
            ->assertStatus(422);
    }

    public function testFailsOnEsportalUsernameMissing()
    {
        $this->user->esportal_username = "verybadusernamethatdoesntexist";
        $this->user->save();

        $this->checkInToTournament([], $this->user)
            ->assertStatus(422);
    }

    public function testFailsOnUserNotRegistered()
    {
        $this->record->users()->detach($this->user->id);

        $this->checkInToTournament([], $this->user)
            ->assertStatus(422);
    }

    public function testFailsOnStateCheckedIn()
    {
        $this->record->users()->updateExistingPivot($this->user->id, [
            "state" => TournamentUserState::CHECKED_IN,
        ]);

        $this->checkInToTournament([], $this->user)
            ->assertStatus(422);
    }

    public function testFailsOnStateRegisteredKicked()
    {
        $this->record->users()->updateExistingPivot($this->user->id, [
            "state" => TournamentUserState::REGISTERED_KICKED,
        ]);

        $this->checkInToTournament([], $this->user)
            ->assertStatus(422);
    }

    public function testFailsOnStateCheckedInKicked()
    {
        $this->record->users()->updateExistingPivot($this->user->id, [
            "state" => TournamentUserState::CHECKED_IN_KICKED,
        ]);

        $this->checkInToTournament([], $this->user)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->checkInToTournament([], null)
            ->assertStatus(401);
    }

    public function testMemberCannotCheckInOtherUser()
    {
        $this->checkInToTournament(["user_id" => 1], User::factory()->create([
            "type" => UserType::MEMBER,
        ]))->assertStatus(403);
    }

    public function testConfirmedCannotCheckInOtherUser()
    {
        $this->checkInToTournament(["user_id" => 1], User::factory()->create([
            "type" => UserType::STREAMER,
        ]))->assertStatus(403);
    }

    /**
     * @param array $data
     * @param User|null $user
     * @return TestResponse
     */
    private function checkInToTournament(array $data, User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.check_in", $this->record), $data);
    }
}
