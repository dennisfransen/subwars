<?php

namespace Tests\Feature;

use App\Http\Enums\UserType;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class UserAttachSponsorTest extends ApiTestCase
{
    private $record, $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = User::factory()->create();
        $this->data["sponsor_id"] = Sponsor::factory()->create()->id;
    }

    public function testAttachesSponsor()
    {
        $this->attachSponsor($this->record);

        $this->assertEquals($this->data["sponsor_id"], $this->record->sponsors()->first()->id);
    }

    public function testReturnsHttpOk()
    {
        $this->attachSponsor($this->record)->assertStatus(200);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->attachSponsor($this->record)
            ->assertStatus(404);
    }

    public function testFailsOnSponsorNotFound()
    {
        $this->data["sponsor_id"] = 20;

        $this->attachSponsor($this->record)
            ->assertStatus(422);
    }

    public function testFailsOnSponsorAlreadyAttached()
    {
        $this->record->sponsors()->attach($this->data["sponsor_id"]);

        $this->attachSponsor($this->record)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->attachSponsor(null)
            ->assertStatus(401);
    }

    public function testUserCannotAttachSponsorToOtherUser() {
        $this->attachSponsor(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testSuperadminCanAttachSponsorToOtherUser() {
        $this->attachSponsor(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function attachSponsor(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("users.attach_sponsor", $this->record), $this->data);
    }
}
