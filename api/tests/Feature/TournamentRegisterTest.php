<?php

namespace Tests\Feature;

use App\Events\TournamentUpdatedEvent;
use App\Events\UserRegisteredToTournamentEvent;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentRegisterTest extends ApiTestCase
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
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->registerToTournament([], $this->user);

        Event::assertDispatchedTimes(UserRegisteredToTournamentEvent::class, 1);
        Event::assertDispatchedTimes(TournamentUpdatedEvent::class, 1);
    }

    public function testRegistersLoggedInUser()
    {
        $this->registerToTournament([], $this->user);

        $this->assertEquals($this->user->id, $this->record->users()->first()->id);
    }

    public function testRegistersWithFirstOrder()
    {
        $this->registerToTournament([], $this->user);

        $this->assertEquals(1, $this->record->users()->first()->pivot->order);
    }

    public function testRegistersWithRisingOrder()
    {
        $this->record->users()->attach(User::factory()->create(), [
            "state" => TournamentUserState::REGISTERED,
        ]);

        $this->registerToTournament([], $this->user);

        $this->assertEquals(2, $this->record->users()->first()->pivot->order);
    }

    public function testRegistersUserByRequest()
    {
        User::find(1)->update(["esportal_username" => "Poppy"]);
        $this->registerToTournament(["user_id" => 1], $this->user);

        $this->assertEquals(1, $this->record->users()->first()->id);
    }

    public function testRegistersWithCorrectState()
    {
        $this->registerToTournament([], $this->user);

        $this->assertEquals(TournamentUserState::REGISTERED, $this->record->users()->first()->pivot->state);
    }

    public function testUpdatesUserEloFromEsportal()
    {
        $this->user->esportal_elo = 0;
        $this->user->save();

        $this->registerToTournament([], $this->user);
        $this->user->refresh();

        $this->assertEquals(1200, $this->user->esportal_elo);
    }

    public function testFailsOnBadEsportalUsernameNull()
    {
        $this->user->esportal_username = null;
        $this->user->save();

        $this->registerToTournament([], $this->user)
            ->assertStatus(422);
    }

    public function testFailsOnEsportalUsernameMissing()
    {
        $this->user->esportal_username = "verybadusernamethatdoesntexist";
        $this->user->save();

        $this->registerToTournament([], $this->user)
            ->assertStatus(422);
    }

    public function testReturnsHttpOk()
    {
        $this->registerToTournament([], $this->user)->assertStatus(200);
    }

    public function testFailsOnDuplicateByAuth()
    {
        $this->record->users()->attach($this->user);

        $this->registerToTournament([], $this->user)
            ->assertStatus(422);
    }

    public function testFailsOnDuplicateByRequest()
    {
        $this->record->users()->attach(1);

        $this->registerToTournament(["user_id" => 1], $this->user)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->registerToTournament([], null)
            ->assertStatus(401);
    }

    public function testMemberCannotRegisterOtherUser()
    {
        $this->registerToTournament(["user_id" => 1], User::factory()->create([
            "type" => UserType::MEMBER,
        ]))->assertStatus(403);
    }

    public function testConfirmedCannotRegisterOtherUser()
    {
        $this->registerToTournament(["user_id" => 1], User::factory()->create([
            "type" => UserType::STREAMER,
        ]))->assertStatus(403);
    }

    /**
     * @param array $data
     * @param User|null $user
     * @return TestResponse
     */
    private function registerToTournament(array $data, User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.register", $this->record), $data);
    }
}
