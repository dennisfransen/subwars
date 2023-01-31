<?php

namespace Tests\Feature;

use App\Events\TeamUpdatedEvent;
use App\Http\Enums\UserType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TeamUpdateTest extends ApiTestCase
{
    private $record, $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Team::factory()->create();
        $this->data["title"] = $this->record->title . " new";
    }

    public function testReturnsHttpOk()
    {
        $this->updateRecordWithUser($this->record->tournament->creator)->assertStatus(200);
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->updateRecordWithUser($this->record->tournament->creator);

        Event::assertDispatchedTimes(TeamUpdatedEvent::class);
    }

    public function testUpdatesTitle()
    {
        $this->updateRecordWithUser($this->record->tournament->creator);
        $this->record->refresh();

        $this->assertEquals($this->data["title"], $this->record->title);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->updateRecordWithUser($this->record->tournament->creator)
            ->assertStatus(404);
    }

    public function testFailsOnBadTitle()
    {
        $this->actingAs($this->record->tournament->creator);

        $this->updateRecord(["title" => null])->assertStatus(422);
    }

    public function testReturnsHttpOkWithoutTitle()
    {
        $this->actingAs($this->record->tournament->creator);

        $this->updateRecord([])->assertStatus(200);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->updateRecordWithUser(null)
            ->assertStatus(401);
    }

    public function testMemberReturnsUnauthenticated()
    {
        $this->updateRecordWithUser(User::factory()->create(["type" => UserType::MEMBER]))
            ->assertStatus(403);
    }

    public function testConfirmedCantUpdateOther()
    {
        $this->updateRecordWithUser(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->record->tournament->coCasters()->attach($coCaster);

        $this->updateRecordWithUser($coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->updateRecordWithUser(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function updateRecordWithUser(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->updateRecord($this->data);
    }

    public function updateRecord(array $data): TestResponse
    {
        return $this->putJson(route("teams.update", $this->record), $data);
    }
}
