<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class UserShowTest extends ApiTestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = User::factory()->create();
    }

    public function testReturnsHttpOk()
    {
        $this->showRecord($this->record)->assertStatus(200);
    }

    public function testReturnsRecord()
    {
        $response = $this->showRecord($this->record);

        $this->assertContains(
            $this->record->username,
            $response->json("data")
        );
        $this->assertEquals($this->record->id, $response->json("data.id"));
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->showRecord()
            ->assertStatus(401);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function showRecord(User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);

        return $this->getJson(route("users.show", $this->record));
    }
}
