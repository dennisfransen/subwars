<?php

namespace Tests\Feature;

use App\Events\TournamentOpenedForCheckInEvent;
use App\Events\TournamentOpenedForRegistrationEvent;
use App\Events\TournamentUpdatedEvent;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Notification;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentOpenCheckInTest extends ApiTestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
    }

    public function testUpdatesCheckInOpenAt()
    {
        $this->openCheckIn($this->record->creator);
        $this->record->refresh();

        $this->assertTrue($this->record->is_open_for_check_in);
    }

    public function testReturnsHttpOk()
    {
        $this->openCheckIn($this->record->creator)->assertStatus(200);
    }

    public function testCreatesNotificationsForRegisteredUsers()
    {
        $users = User::factory()->count(2)->create();
        $this->record->users()->attach($users->pluck("id"), [
            "state" => TournamentUserState::REGISTERED
        ]);
        $this->record->users()->attach(User::factory()->create(), [
            "state" => TournamentUserState::REGISTERED_KICKED,
        ]);

        $this->openCheckIn($this->record->creator);

        $this->assertCount(2, Notification::all());
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->openCheckIn($this->record->creator)
            ->assertStatus(404);
    }

    public function testFailsOnRecordOpenForRegistration()
    {
        $this->record->check_in_open_at = Carbon::now();
        $this->record->save();

        $this->openCheckIn($this->record->creator)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->openCheckIn(null)
            ->assertStatus(401);
    }

    public function testConfirmedCantUpdateAny()
    {
        $this->openCheckIn(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->record->coCasters()->attach($coCaster);

        $this->openCheckIn($coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->openCheckIn(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    public function testDispatchesTournamentUpdatedEvent()
    {
        Event::fake();

        $tournament = $this->record;

        $this->openCheckIn($this->record->creator);

        Event::assertDispatched(function (TournamentUpdatedEvent $event) use ($tournament) {
            if ($event->broadcastOn()->name !== "App.Models.Tournament")
                return false;

            return $event->tournament->id === $tournament->id;
        });
    }

    public function testDispatchesOpenedForCheckInEventOnTournamentChannel()
    {
        Event::fake();

        $tournament = $this->record;

        $this->openCheckIn($this->record->creator);

        Event::assertDispatched(function (TournamentOpenedForCheckInEvent $event) use ($tournament) {
            if ($event->broadcastOn()->name !== "App.Models.Tournament." . $tournament->id)
                return false;

            return $event->tournament->id === $tournament->id;
        });
    }

    public function testDispatchesOpenedForCheckInEventOnRelatedUserChannels()
    {
        Event::fake();

        $tournament = $this->record;
        $tournament->users()->attach(User::factory()->create());

        $this->openCheckIn($this->record->creator);

        Event::assertDispatched(function (TournamentOpenedForCheckInEvent $event) use ($tournament) {
            return ($event->broadcastOn()->name === "App.Models.User." . $tournament->users()->first()->id);
        });
    }

    public function testDoesNotDispatchOpenedForCheckInEventOnCreatorChannel()
    {
        Event::fake();

        $tournament = $this->record;

        $this->openCheckIn($this->record->creator);

        Event::assertNotDispatched(function (TournamentOpenedForCheckInEvent $event) use ($tournament) {
            return ($event->broadcastOn()->name === "App.Models.User." . $tournament->creator->id);
        });
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function openCheckIn(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.open_check_in", $this->record));
    }
}
