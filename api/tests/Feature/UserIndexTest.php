<?php

namespace Tests\Feature;

use App\Events\TournamentUpdatedEvent;
use App\Http\Enums\UserType;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class UserIndexTest extends ApiTestCase
{
    private $records, $guest, $member, $confirmed, $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guest = User::factory()->create(["type" => UserType::GUEST]);
        $this->member = User::factory()->create(["type" => UserType::MEMBER]);
        $this->confirmed = User::factory()->create(["type" => UserType::STREAMER]);
        $this->superadmin = User::factory()->create(["type" => UserType::SUPERADMIN]);

        $this->collectUsers();
    }

    public function testReturnsAllRecords()
    {
        $response = $this->indexRecords();

        $this->assertCount(4, $response->json("data"));
        $this->assertEquals(
            $this->records->pluck("id"),
            $this->getJsonCollection($response)->pluck("id")
        );
    }

    public function testReturnsOnlyStreamers()
    {
        $this->member->streamer = true;
        $this->member->save();
        $this->member->refresh();
        $this->collectUsers();

        $response = $this->getJson(route("users.index", ["streamers" => true]));

        $this->assertCount(1, $response->json("data"));
        $this->assertEquals($this->member->id, $response->json("data")[0]["id"]);
    }

    /**
     * @return TestResponse
     */
    private function indexRecords(): TestResponse
    {
        return $this->getJson(route("users.index"));
    }

    /**
     * @param TestResponse $response
     * @return Collection
     */
    private function getJsonCollection(TestResponse $response): Collection
    {
        return collect($response->json("data"));
    }

    private function collectUsers(): void
    {
        $this->records = collect([$this->guest, $this->member, $this->confirmed, $this->superadmin]);
    }
}
