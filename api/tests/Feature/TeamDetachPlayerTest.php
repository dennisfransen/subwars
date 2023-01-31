<?php

namespace Tests\Feature;

use App\Events\PlayerDetachedFromTeamEvent;
use App\Http\Enums\UserType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TeamDetachPlayerTest extends ApiTestCase
{
    private $record, $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Team::factory()->create();
        $this->record->users()->attach(User::factory()->count(5)->create()->pluck("id"));
        $this->data["user_id"] = $this->record->users()->first()->id;
    }

    public function testDispatchesEventOnTournamentChannel()
    {
        Event::fake();

        $team = $this->record;

        $this->detachPlayer($this->record->tournament->creator);

        Event::assertDispatched(function (PlayerDetachedFromTeamEvent $event) use ($team) {
            if ($event->broadcastOn()->name !== "App.Models.Tournament." . $team->tournament->id)
                return false;

            return $event->id === $team->id;
        });
    }

    public function testDispatchesEventOnUserChannel()
    {
        Event::fake();

        $team = $this->record;
        $userId = $this->data["user_id"];

        $this->detachPlayer($this->record->tournament->creator);

        Event::assertDispatched(function (PlayerDetachedFromTeamEvent $event) use ($team, $userId) {
            if ($event->broadcastOn()->name !== "App.Models.User." . $userId)
                return false;

            return $event->id === $team->id;
        });
    }

    public function testDoesNotDispatchEventOnCreatorChannel()
    {
        Event::fake();

        $team = $this->record;

        $this->detachPlayer($this->record->tournament->creator);

        Event::assertNotDispatched(function (PlayerDetachedFromTeamEvent $event) use ($team) {
            return ($event->broadcastOn()->name === "App.Models.User." . $team->tournament->creator->id);
        });
    }

    public function testReturnsHttpOk()
    {
        $this->detachPlayer($this->record->tournament->creator)->assertStatus(200);
    }

    public function testPlayerGetsDetached()
    {
        $this->detachPlayer($this->record->tournament->creator);

        $this->assertCount(4, $this->record->users);
        $this->assertNotContains($this->data["user_id"], $this->record->users->pluck("id"));
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->detachPlayer($this->record->tournament->creator)
            ->assertStatus(404);
    }

    public function testFailsOnUserNotFound()
    {
        $this->data["user_id"] = 10;

        $this->detachPlayer($this->record->tournament->creator)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->detachPlayer(null)
            ->assertStatus(401);
    }

    public function testMemberReturnsUnauthenticated()
    {
        $this->detachPlayer(User::factory()->create(["type" => UserType::MEMBER]))
            ->assertStatus(403);
    }

    public function testConfirmedCantUpdateOther()
    {
        $this->detachPlayer(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->record->tournament->coCasters()->attach($coCaster);

        $this->detachPlayer($coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->detachPlayer(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function detachPlayer(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("teams.detach_player", $this->record), $this->data);
    }
}
