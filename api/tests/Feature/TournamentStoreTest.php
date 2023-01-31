<?php

namespace Tests\Feature;

use App\Events\TournamentCreatedEvent;
use App\Http\Enums\UserType;
use App\Models\Bracket;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class TournamentStoreTest extends ApiTestCase
{
    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data["title"] = "New Tournament";
        $this->data["description"] = "New Description";
        $this->data["rules"] = "New Rules";
        $this->data["bracket_id"] = Bracket::factory()->create()->id;
        $this->data["max_teams"] = 2;
        $this->data["visible_at"] = Carbon::now()->addDays(2)->toDateTimeString();
        $this->data["registration_open_at"] = Carbon::now()->addDays(2)->toDateTimeString();
        $this->data["live_at"] = Carbon::now()->addDays(2)->toDateTimeString();
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->storeRecord([]);

        Event::assertDispatchedTimes(TournamentCreatedEvent::class, 2);
    }

    public function testStoresTitle()
    {
        $this->storeRecord();

        $this->assertEquals($this->data["title"], Tournament::first()->title);
    }

    public function testReturnsHttpCreated()
    {
        $this->storeRecord(["title" => "New tournament"])->assertStatus(201);
    }

    public function testFailsOnDuplicateTitle()
    {
        Tournament::factory()->create(["title" => "Old"]);

        $this->storeRecord(["title" => "Old"])
            ->assertStatus(422);
    }

    public function testFailsWithoutTitle()
    {
        unset($this->data["title"]);

        $this->storeRecordAndAssertFail([]);
    }

    public function testFailsOnBadMaxTeams()
    {
        $this->storeRecordAndAssertFail(["max_teams" => "b"]);
        $this->storeRecordAndAssertFail(["max_teams" => null]);
    }

    public function testFailsOnBadMinElo()
    {
        $this->storeRecordAndAssertFail(["min_elo" => "b"]);
        $this->storeRecordAndAssertFail(["min_elo" => null]);
    }

    public function testStoresWithMaxEloOnly()
    {
        $this->storeRecord(["max_elo" => 1])
            ->assertStatus(201);
    }

    public function testFailsOnBadMaxElo()
    {
        $this->storeRecordAndAssertFail(["max_elo" => "b"]);
        $this->storeRecordAndAssertFail(["max_elo" => null]);
        $this->storeRecordAndAssertFail([
            "min_elo" => 5,
            "max_elo" => 4,
        ]);
    }

    public function testFailsOnBadVisibleAt()
    {
        $this->storeRecordAndAssertFail(["visible_at" => "b"]);
        $this->storeRecordAndAssertFail(["visible_at" => null]);
    }

    public function testFailsWithoutVisibleAt()
    {
        unset($this->data["visible_at"]);

        $this->storeRecordAndAssertFail([]);
    }

    public function testFailsOnBadLiveAt()
    {
        $this->storeRecordAndAssertFail(["live_at" => "b"]);
        $this->storeRecordAndAssertFail(["live_at" => null]);
    }

    /**
     * @param array $data
     */
    private function storeRecordAndAssertFail(array $data)
    {
        $this->storeRecord($data)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->storeRecord(["title" => "Tournament"], null)
            ->assertStatus(401);
    }

    public function testBannedReturnsForbidden()
    {
        $this->storeRecord(["title" => "Tournament"], UserType::BANNED)
            ->assertStatus(403);
    }

    public function testGuestReturnsForbidden()
    {
        $this->storeRecord(["title" => "Tournament"], UserType::GUEST)
            ->assertStatus(403);
    }

    public function testMemberReturnsForbidden()
    {
        $this->storeRecord(["title" => "Tournament"], UserType::MEMBER)
            ->assertStatus(403);
    }

    /**
     * @param array|null $data
     * @param int|null $userType
     * @return TestResponse
     */
    private function storeRecord(array $data = null, ?int $userType = UserType::STREAMER): TestResponse
    {
        if ($data)
            $data = array_merge($this->data, $data);
        else
            $data = $this->data;

        if ($userType !== null)
            $this->actingAs(User::factory()->create(["type" => $userType]));

        return $this->postJson(route("tournaments.store"), $data);
    }
}
