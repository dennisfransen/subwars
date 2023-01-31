<?php

namespace Tests\Feature;

use App\Events\TournamentUpdatedEvent;
use App\Events\UserDeRegisteredFromTournamentEvent;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentDeRegisterTest extends ApiTestCase
{
    private $record, $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
        $this->user = User::factory()->create([
            "type" => UserType::SUPERADMIN,
            "esportal_username" => "MrMotoX",
        ]);

        $this->record->users()->attach($this->user, [
            "state" => TournamentUserState::REGISTERED,
        ]);
        $this->record->users()->attach(User::factory()->create()->id, [
            "state" => TournamentUserState::REGISTERED,
        ]);
    }

    public function testDispatchesEventOnTournamentChannel()
    {
        Event::fake();

        $tournament = $this->record;
        $user = $this->user;

        $this->deRegisterFromTournament([], $this->user);

        Event::assertDispatched(function (UserDeRegisteredFromTournamentEvent $event) use ($tournament, $user) {
            if ($event->broadcastOn()->name !== "App.Models.Tournament." . $tournament->id)
                return false;

            return $event->user_id === $user->id;
        });
    }

    public function testDispatchesEventOnUserChannel()
    {
        Event::fake();

        $user = $this->user;

        $this->deRegisterFromTournament([], $this->user);

        Event::assertDispatched(function (UserDeRegisteredFromTournamentEvent $event) use ($user) {
            if ($event->broadcastOn()->name !== "App.Models.User." . $user->id)
                return false;

            return $event->user_id === $user->id;
        });
    }

    public function testDeRegistersLoggedInUser()
    {
        $this->deRegisterFromTournament([], $this->user);

        $this->assertCount(1, $this->record->users);
    }

    public function testReturnsHttpOk()
    {
        $this->deRegisterFromTournament([], $this->user)->assertStatus(200);
    }

    public function testFailsOnUserNotRegistered()
    {
        $this->record->users()->detach($this->user->id);

        $this->deRegisterFromTournament([], $this->user)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->deRegisterFromTournament([], null)
            ->assertStatus(401);
    }

    public function testMemberCannotDeRegisterOtherUser()
    {
        $this->deRegisterFromTournament(["user_id" => 1], User::factory()->create([
            "type" => UserType::MEMBER,
        ]))->assertStatus(403);
    }

    public function testConfirmedCannotDeRegisterOtherUser()
    {
        $this->deRegisterFromTournament(["user_id" => 1], User::factory()->create([
            "type" => UserType::STREAMER,
        ]))->assertStatus(403);
    }

    public function testAdminCanDeRegisterOtherUser()
    {
        $this->deRegisterFromTournament(["user_id" => 3], $this->user)->assertStatus(200);
    }

    /**
     * @param array $data
     * @param User|null $user
     * @return TestResponse
     */
    private function deRegisterFromTournament(array $data, User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.de_register", $this->record), $data);
    }
}
