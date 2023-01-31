<?php

namespace Tests\Feature;

use App\Events\TeamDestroyedEvent;
use App\Http\Enums\UserType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TeamDestroyTest extends ApiTestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Team::factory()->create();
    }

    public function testReturnsHttpOk()
    {
        $this->destroyRecord($this->record->tournament->creator)->assertStatus(200);
    }

    public function testDispatchesEventOnTournamentChannel()
    {
        Event::fake();

        $team = $this->record;

        $this->destroyRecord($this->record->tournament->creator);

        Event::assertDispatched(function (TeamDestroyedEvent $event) use ($team) {
            if ($event->broadcastOn()->name !== "App.Models.Tournament")
                return false;

            return $event->id === $team->id;
        });
    }

    public function testDispatchesEventOnUserChannel()
    {
        Event::fake();

        $team = $this->record;
        $user = User::factory()->create();
        $team->users()->attach($user);

        $this->destroyRecord($this->record->tournament->creator);

        Event::assertDispatched(function (TeamDestroyedEvent $event) use ($team, $user) {
            if ($event->broadcastOn()->name !== "App.Models.User." . $user->id)
                return false;

            return $event->id === $team->id;
        });
    }

    public function testDoesNotDispatchEventOnCreatorChannel()
    {
        Event::fake();

        $team = $this->record;

        $this->destroyRecord($this->record->tournament->creator);

        Event::assertNotDispatched(function (TeamDestroyedEvent $event) use ($team) {
            return ($event->broadcastOn()->name === "App.Models.User." . $team->tournament->creator->id);
        });
    }

    public function testRecordGetsDestroyed()
    {
        $this->destroyRecord($this->record->tournament->creator);

        $this->assertCount(0, Team::where("id", $this->record->id)->get());
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->destroyRecord($this->record->tournament->creator)
            ->assertStatus(404);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->destroyRecord(null)
            ->assertStatus(401);
    }

    public function testMemberReturnsUnauthenticated()
    {
        $this->destroyRecord(User::factory()->create(["type" => UserType::MEMBER]))
            ->assertStatus(403);
    }

    public function testConfirmedCantUpdateOther()
    {
        $this->destroyRecord(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->record->tournament->coCasters()->attach($coCaster);

        $this->destroyRecord($coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->destroyRecord(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function destroyRecord(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->deleteJson(route("teams.destroy", $this->record));
    }
}
