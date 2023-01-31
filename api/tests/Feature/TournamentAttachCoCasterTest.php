<?php

namespace Tests\Feature;

use App\Http\Enums\CasterRole;
use App\Http\Enums\UserType;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentAttachCoCasterTest extends ApiTestCase
{
    private $record, $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
        $this->data = [
            "user_id" => User::factory()->create()->id,
            "role" => (new CasterRole())->getStringOfInteger(CasterRole::CO_CASTER),
        ];
    }

    // TODO Add role to tests

    public function testAttachesCoCaster()
    {
        $this->attachCoCaster($this->record->creator);

        $this->assertEquals($this->data["user_id"], $this->record->coCasters()->first()->id);
    }

    public function testReturnsHttpOk()
    {
        $this->attachCoCaster($this->record->creator)->assertStatus(200);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->attachCoCaster($this->record->creator)
            ->assertStatus(404);
    }

    public function testFailsOnUserNotFound()
    {
        $this->data["user_id"] = 20;

        $this->attachCoCaster($this->record->creator)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->attachCoCaster(null)
            ->assertStatus(401);
    }

    public function testConfirmedCantUpdateAny()
    {
        $this->attachCoCaster(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testCoCasterCanUpdate()
    {
        $coCaster = User::factory()->create();
        $this->record->coCasters()->attach($coCaster);

        $this->attachCoCaster($coCaster)
            ->assertStatus(200);
    }

    public function testSuperadminCanUpdateAny()
    {
        $this->attachCoCaster(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param array $data
     * @param User|null $user
     * @return TestResponse
     */
    private function attachCoCaster(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("tournaments.attach_co_caster", $this->record), $this->data);
    }
}
