<?php

namespace Tests\Feature;

use App\Http\Enums\UserType;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class UserDetachSponsorTest extends ApiTestCase
{
    private $record, $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = User::factory()->create();
        $this->data["sponsor_id"] = Sponsor::factory()->create()->id;
        $this->record->sponsors()->attach($this->data["sponsor_id"]);
    }

    public function testDetachesSponsor()
    {
        $this->detachSponsor($this->record);

        $this->assertCount(0, $this->record->sponsors);
    }

    public function testReturnsHttpOk()
    {
        $this->detachSponsor($this->record)->assertStatus(200);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->detachSponsor($this->record)
            ->assertStatus(404);
    }

    public function testFailsOnSponsorNotFound()
    {
        $this->data["sponsor_id"] = 20;

        $this->detachSponsor($this->record)
            ->assertStatus(422);
    }

    public function testFailsOnSponsorAlreadyDetached()
    {
        $this->record->sponsors()->detach($this->data["sponsor_id"]);

        $this->detachSponsor($this->record)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->detachSponsor(null)
            ->assertStatus(401);
    }

    public function testUserCannotAttachSponsorToOtherUser() {
        $this->detachSponsor(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    public function testSuperadminCanAttachSponsorToOtherUser() {
        $this->detachSponsor(User::factory()->create(["type" => UserType::SUPERADMIN]))
            ->assertStatus(200);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function detachSponsor(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("users.detach_sponsor", $this->record), $this->data);
    }
}
