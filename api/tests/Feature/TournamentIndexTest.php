<?php

namespace Tests\Feature;

use App\Events\TournamentUpdatedEvent;
use App\Http\Enums\UserType;
use App\Http\Resources\TournamentResource;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentIndexTest extends ApiTestCase
{
    private $records;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->records = Tournament::factory()->count(2)->create([
            "user_id" => $user->id,
        ]);
    }

    public function testReturnsAllRecords()
    {
        $response = $this->indexRecords();

        $this->assertCount(2, $response->json("data"));
        $this->assertEquals(
            $this->records->pluck("id"),
            $this->getJsonCollection($response)->pluck("id")
        );
    }

    public function testReturnsWithSponsors()
    {
        foreach ($this->records as $record) {
            $record->sponsors()->attach(Sponsor::factory()->count(2)->create());
        }
        $expectedSponsorIds = Tournament::all()->flatMap(function ($tournament) {
            return $tournament->sponsors;
        })->pluck("id");

        $response = $this->indexRecords();

        $actualSponsorIds = $this->getJsonCollection($response)->flatMap(function ($tournament) {
            return $tournament["sponsors"];
        })->pluck("id");

        $this->assertEquals(
            $expectedSponsorIds,
            $actualSponsorIds
        );
    }

    // TODO Test that only visible are shown

    /**
     * @return TestResponse
     */
    private function indexRecords(): TestResponse
    {
        return $this->getJson(route("tournaments.index"));
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
