<?php

namespace Tests\Feature;

use App\Http\Enums\UserType;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentDetachCoCasterTest extends ApiTestCase
{
    private $record, $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
        $this->record->coCasters()->attach(User::factory()->count(2)->create()->pluck("id"));
        $this->data["user_id"] = $this->record->coCasters()->first()->id;
    }

    public function testDetachesCoCaster()
    {
        $this->detachCoCaster($this->record->creator);

        $this->assertCount(1, $this->record->coCasters);
        $this->assertNotEquals($this->data["user_id"], $this->record->coCasters()->first()->id);
    }

    public function testReturnsHttpOk()
    {
        $this->detachCoCaster($this->record->creator)->assertStatus(200);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->detachCoCaster($this->record->creator)
            ->assertStatus(404);
    }

    public function testFailsOnUserNotAttached()
    {
        $this->record->coCasters()->detach($this->data["user_id"]);

        $this->detachCoCaster($this->record->creator)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->detachCoCaster(null)
            ->assertStatus(401);
    }

    public function testConfirmedCantUpdateAny()
    {
        $this->detachCoCaster(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->record->coCasters()->attach($coCaster);

        $this->detachCoCaster($coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->detachCoCaster(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param array $data
     * @param User|null $user
     * @return TestResponse
     */
    private function detachCoCaster(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.detach_co_caster", $this->record), $this->data);
    }
}
