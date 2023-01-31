<?php

namespace Tests\Feature;

use App\Events\TournamentDestroyedEvent;
use App\Http\Enums\UserType;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentDestroyTest extends ApiTestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create([
            "user_id" => User::factory()->create(["type" => UserType::STREAMER])->id,
        ]);
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->destroyRecord($this->record->creator);

        Event::assertDispatchedTimes(TournamentDestroyedEvent::class, 3);
    }

    public function testReturnsHttpOk()
    {
        $this->destroyRecord($this->record->creator)->assertStatus(200);
    }

    public function testRecordGetsDestroyed()
    {
        $this->destroyRecord($this->record->creator);

        $this->assertCount(0, Tournament::where("id", $this->record->id)->get());
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->destroyRecord($this->record->creator)
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

        return $this->deleteJson(route("tournaments.destroy", $this->record));
    }
}
