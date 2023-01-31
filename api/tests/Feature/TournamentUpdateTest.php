<?php

namespace Tests\Feature;

use App\Events\TournamentUpdatedEvent;
use App\Http\Enums\UserType;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentUpdateTest extends ApiTestCase
{
    private $record, $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();

        $this->data = [
            "title" => $this->record->title . " new",
        ];
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->updateRecord($this->data, $this->record->creator);

        Event::assertDispatchedTimes(TournamentUpdatedEvent::class, 2);
    }

    public function testUpdatesTitle()
    {
        $this->updateRecord($this->data, $this->record->creator);

        $this->assertEquals($this->data["title"], Tournament::first()->title);
    }

    public function testReturnsHttpOk()
    {
        $this->updateRecord([], $this->record->creator)->assertStatus(200);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->updateRecord([], $this->record->creator)
            ->assertStatus(404);
    }

    public function testFailsOnDuplicateTitle()
    {
        Tournament::factory()->create(["title" => "Existing"]);

        $this->updateRecord(["title" => "Existing"], $this->record->creator)
            ->assertStatus(422);
    }

    public function testDoesntFailOnDuplicateTitleBySelf()
    {
        $this->updateRecord(["title" => Tournament::first()->title], $this->record->creator)
            ->assertStatus(200);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->updateRecord($this->data, null)
            ->assertStatus(401);
    }

    public function testConfirmedCantUpdateAny()
    {
        $this->updateRecord($this->data, User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->record->coCasters()->attach($coCaster);

        $this->updateRecord($this->data, $coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->updateRecord($this->data, User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param array $data
     * @param User|null $user
     * @return TestResponse
     */
    private function updateRecord(array $data, User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.update", $this->record), $data);
    }
}
