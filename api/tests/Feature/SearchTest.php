<?php

namespace Tests\Feature;

use App\Http\Enums\UserType;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class SearchTest extends ApiTestCase
{
    private $data, $streamers, $tournaments;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data["needle"] = "Streamer";

        $this->streamers[] = User::factory()->create([
            "username" => "Lungan",
            "type" => UserType::STREAMER,
        ]);
        $this->streamers[] = User::factory()->create([
            "username" => "Chillalungando",
            "type" => UserType::STREAMER,
        ]);
        $this->streamers[] = User::factory()->create([
            "username" => "Piraten",
            "type" => UserType::STREAMER,
        ]);

        $this->tournaments[] = Tournament::factory()->create([
            "user_id" => $this->streamers[0]->id,
            "title" => "Torsdagsfrullen",
        ]);
        $this->tournaments[] = Tournament::factory()->create([
            "user_id" => $this->streamers[1]->id,
            "title" => "Fredagskycklingen",
        ]);
        $this->tournaments[] = Tournament::factory()->create([
            "user_id" => $this->streamers[1]->id,
            "title" => "Lördagsturen",
        ]);
        $this->tournaments[] = Tournament::factory()->create([
            "user_id" => $this->streamers[2]->id,
            "title" => "Söndagsfrullen",
        ]);
    }

    public function testFindsStreamersByUsername()
    {
        $response = $this->search(["needle" => "lung"]);

        $this->assertCount(2, $response->json("data.streamers"));
        $this->assertEquals([1, 2], collect($response->json("data.streamers"))->pluck("id")->toArray());
    }

    public function testFindsTournamentsByTitle()
    {
        $response = $this->search(["needle" => "frull"]);

        $this->assertCount(2, $response->json("data.tournaments"));
        $this->assertEquals([1, 4], collect($response->json("data.tournaments"))->pluck("id")->toArray());
    }

    public function testReturnsHttpOk()
    {
        $this->search()->assertStatus(200);
    }

    public function testFailsOnBadNeedle()
    {
        $this->storeRecordAndAssertFail(["needle" => null]);
    }

    /**
     * @param array $data
     */
    private function storeRecordAndAssertFail(array $data)
    {
        $this->search($data)
            ->assertStatus(422);
    }

    public function testGuestReturnsHttpOk()
    {
        $this->search([], null)
            ->assertStatus(200);
    }

    /**
     * @param array|null $data
     * @param int|null $userType
     * @return TestResponse
     */
    private function search(array $data = null, ?int $userType = UserType::STREAMER): TestResponse
    {
        if ($data)
            $data = array_merge($this->data, $data);
        else
            $data = $this->data;

        if ($userType !== null)
            $this->actingAs(User::factory()->create(["type" => $userType]));

        return $this->postJson(route("search"), $data);
    }
}
