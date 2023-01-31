<?php

namespace Tests\Feature;

use App\Models\Sponsor;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentShowTest extends ApiTestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
    }

    public function testReturnsHttpOk()
    {
        $this->showRecord()->assertStatus(200);
    }

    public function testReturnsRecord()
    {
        $response = $this->showRecord();

        $this->assertContains(
            $this->record->title,
            $response->json("data")
        );
        $this->assertEquals($this->record->id, $response->json("data.id"));
    }

    public function testReturnsSponsors()
    {
        $this->record->sponsors()->attach(Sponsor::factory()->count(2)->create());

        $response = $this->showRecord();

        $this->assertArrayHasKey("sponsors", $response->json("data"));
        $this->assertContains($this->record->sponsors()->first()->title, $response->json("data.sponsors")[0]);
        $this->assertCount(2, $response->json("data.sponsors"));
    }

    public function testReturnsTeams()
    {
        $users = User::factory()->count(2)->create();
        $team = Team::factory()->create([
            "tournament_id" => $this->record->id,
        ]);
        $team->users()->attach($users->pluck("id"));
        $this->record->users()->attach($users[0]);
        $this->record->users()->attach($users[1]);

        $response = $this->showRecord();

        $this->assertArrayHasKey("teams", $response->json("data"));
        $this->assertCount(2, collect($response->json("data.teams")[0]["users"])->pluck("elo"));
    }

    // TODO Test that registered array contains users with checked_in true and false

    /**
     * @return TestResponse
     */
    private function showRecord(): TestResponse
    {
        return $this->getJson(route("tournaments.show", $this->record));
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
