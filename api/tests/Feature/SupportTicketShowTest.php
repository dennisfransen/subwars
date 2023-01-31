<?php

namespace Tests\Feature;

use App\Events\TournamentUpdatedEvent;
use App\Http\Enums\UserType;
use App\Http\Resources\TournamentResource;
use App\Models\Sponsor;
use App\Models\SupportTicket;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class SupportTicketShowTest extends ApiTestCase
{
    private $record, $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(["type" => UserType::SUPERADMIN]);
        $this->record = SupportTicket::factory()->create(["sender_id" => $this->user->id]);
    }

    public function testReturnsHttpOk()
    {
        $this->showRecord($this->user)->assertStatus(200);
    }

    public function testReturnsRecord()
    {
        $response = $this->showRecord($this->user);

        $this->assertContains(
            $this->record->description,
            $response->json("data")
        );
        $this->assertEquals($this->record->id, $response->json("data.id"));
    }

    public function testReturnsSender()
    {
        $response = $this->showRecord($this->user);

        $this->assertArrayHasKey("sender", $response->json("data"));
        $this->assertEqualValue($response, $this->record->sender->username, "data.sender.username");
    }

    public function testGuestReturnsUnauthorized() {
        $this->showRecord()->assertStatus(401);
    }

    public function testConfirmedReturnsForbidden() {
        $this->showRecord(User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function showRecord(User $user = null): TestResponse
    {
        if ($user)
            $this->actingAs($user);

        return $this->getJson(route("support_tickets.show", $this->record));
    }

    /**
     * @param TestResponse $response
     * @param $expectedValue
     * @param string $key
     */
    private function assertEqualValue(TestResponse $response, $expectedValue, string $key)
    {
        $this->assertEquals($expectedValue, $response->json($key));
    }
}
