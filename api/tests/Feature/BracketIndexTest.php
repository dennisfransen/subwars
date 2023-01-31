<?php

namespace Tests\Feature;

use App\Models\Bracket;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class BracketIndexTest extends ApiTestCase
{
    private $records;

    protected function setUp(): void
    {
        parent::setUp();

        $this->records = Bracket::factory()->count(2)->create();
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

    /**
     * @return TestResponse
     */
    private function indexRecords(): TestResponse
    {
        return $this->getJson(route("brackets.index"));
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
