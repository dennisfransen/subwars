<?php

namespace Tests\Feature;

use App\Events\TournamentUpdatedEvent;
use App\Http\Enums\UserType;
use App\Models\Price;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class PriceStoreTest extends ApiTestCase
{
    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake("local");

        $this->data["title"] = "New Tournament";
        $this->data["tournament_id"] = Tournament::factory()->create()->id;
//        $this->data["image"] = UploadedFile::fake()->image("image.jpg");
    }

    public function testDispatchesEvent()
    {
        Event::fake();

        $this->storeRecord([], UserType::SUPERADMIN);

        Event::assertDispatchedTimes(TournamentUpdatedEvent::class);
    }

    public function testStoresTitle()
    {
        $this->storeRecord([], UserType::SUPERADMIN);

        $this->assertEquals($this->data["title"], Price::first()->title);
    }

    public function testReturnsHttpCreated()
    {
        $this->storeRecord([], UserType::SUPERADMIN)->assertStatus(201);
    }

    public function testFailsWithoutTitle()
    {
        unset($this->data["title"]);

        $this->storeRecordAndAssertFail([]);
    }

    public function testFailsOnBadTournament()
    {
        $this->storeRecordAndAssertFail(["tournament_id" => 0]);
        $this->storeRecordAndAssertFail(["tournament_id" => Tournament::all()->count() + 1]);
    }

    public function testFailsWithoutTournament()
    {
        unset($this->data["tournament_id"]);

        $this->storeRecordAndAssertFail([]);
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
        $this->storeRecord([], null)
            ->assertStatus(401);
    }

    public function testBannedReturnsForbidden()
    {
        $this->storeRecord([], UserType::BANNED)
            ->assertStatus(403);
    }

    public function testGuestReturnsForbidden()
    {
        $this->storeRecord([], UserType::GUEST)
            ->assertStatus(403);
    }

    public function testMemberReturnsForbidden()
    {
        $this->storeRecord([], UserType::MEMBER)
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

        return $this->postJson(route("prices.store"), $data);
    }
}
