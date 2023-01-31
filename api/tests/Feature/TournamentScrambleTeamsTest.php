<?php

namespace Tests\Feature;

use App\Events\TeamsScrambledEvent;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentScrambleTeamsTest extends ApiTestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();

        $users = User::factory()->count(14)->create();
        $this->record->users()->attach($users->pluck("id"), [
            "state" => TournamentUserState::CHECKED_IN,
        ]);
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->scrambleTeams($this->record->creator);

        Event::assertDispatchedTimes(TeamsScrambledEvent::class, 1);
    }

    public function testScramblesTeams()
    {
        $this->scrambleTeams($this->record->creator);

        $this->assertCount(2, $this->record->teams);
        $this->assertCount(4, $this->record->reserve);
    }

    public function testReturnsHttpOk()
    {
        $this->scrambleTeams($this->record->creator)->assertStatus(200);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->scrambleTeams($this->record->creator)
            ->assertStatus(404);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->scrambleTeams(null)
            ->assertStatus(401);
    }

    public function testConfirmedCantUpdateAny()
    {
        $this->scrambleTeams(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->record->coCasters()->attach($coCaster);

        $this->scrambleTeams($coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->scrambleTeams(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function scrambleTeams(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.scramble_teams", $this->record));
    }
}
