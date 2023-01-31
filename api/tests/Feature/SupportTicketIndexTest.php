<?php

namespace Tests\Feature;

use App\Http\Enums\UserType;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class SupportTicketIndexTest extends ApiTestCase
{
    private $records, $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(["type" => UserType::SUPERADMIN]);
        $this->records = SupportTicket::factory()->count(2)->create();
    }

    public function testReturnsAllRecords()
    {
        $this->actingAs($this->user);
        $response = $this->indexRecords();

        $this->assertCount(2, $response->json("data"));
        $this->assertEquals(
            $this->records->pluck("id"),
            $this->getJsonCollection($response)->pluck("id")
        );
    }

    public function testGuestReturnsUnauthorized() {
        $this->indexRecords()->assertStatus(401);
    }

    public function testNonAdminReturnsForbidden() {
        $this->actingAs(User::factory()->create(["type" => UserType::STREAMER]));

        $this->indexRecords()->assertStatus(403);
    }

    /**
     * @return TestResponse
     */
    private function indexRecords(): TestResponse
    {
        return $this->getJson(route("support_tickets.index"));
    }

    /**
     * @param TestResponse $response
     * @return Collection
     */
    private function getJsonCollection(TestResponse $response): Collection
    {
        return collect($response->json("data"));
    }
}
