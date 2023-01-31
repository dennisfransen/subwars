<?php

namespace Tests\Feature;

use App\Events\TournamentOpenedForRegistrationEvent;
use App\Events\TournamentUpdatedEvent;
use App\Http\Enums\UserType;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentOpenRegistrationTest extends ApiTestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->openRegistration($this->record->creator);

        Event::assertDispatchedTimes(TournamentOpenedForRegistrationEvent::class, 1);
        Event::assertDispatchedTimes(TournamentUpdatedEvent::class, 1);
    }

    public function testUpdatesRegistrationOpenAt()
    {
        $this->openRegistration($this->record->creator);
        $this->record->refresh();

        $this->assertTrue($this->record->is_open_for_registration);
    }

    public function testReturnsHttpOk()
    {
        $this->openRegistration($this->record->creator)->assertStatus(200);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->openRegistration($this->record->creator)
            ->assertStatus(404);
    }

    public function testFailsOnRecordOpenForRegistration()
    {
        $this->record->registration_open_at = Carbon::now();
        $this->record->save();

        $this->openRegistration($this->record->creator)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->openRegistration(null)
            ->assertStatus(401);
    }

    public function testConfirmedCantUpdateAny()
    {
        $this->openRegistration(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->record->coCasters()->attach($coCaster);

        $this->openRegistration($coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->openRegistration(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function openRegistration(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.open_registration", $this->record));
    }
}
