<?php

namespace Tests\Feature;

use App\Http\Enums\UserType;
use App\Models\Bracket;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class BracketStoreTest extends ApiTestCase
{
    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data["title"] = "New Bracket";
    }

    public function testStoresTitle()
    {
        $this->storeRecord();

        $this->assertEquals($this->data["title"], Bracket::first()->title);
    }

    public function testReturnsHttpCreated()
    {
        $this->storeRecord()->assertStatus(201);
    }

    public function testFailsOnDuplicateTitle()
    {
        Bracket::factory()->create(["title" => "Old"]);

        $this->storeRecord(["title" => "Old"])
            ->assertStatus(422);
    }

    public function testFailsWithoutTitle()
    {
        unset($this->data["title"]);

        $this->storeRecordAndAssertFail([]);
    }

    /**
     * @param string $key
     * @param $value
     */
    private function storeRecordAndAssertFail(array $data)
    {
        $this->storeRecord($data)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->storeRecord(["title" => "Bracket"], null)
            ->assertStatus(401);
    }

    public function testBannedReturnsForbidden()
    {
        $this->storeRecord(["title" => "Bracket"], UserType::BANNED)
            ->assertStatus(403);
    }

    public function testGuestReturnsForbidden()
    {
        $this->storeRecord(["title" => "Bracket"], UserType::GUEST)
            ->assertStatus(403);
    }

    public function testMemberReturnsForbidden()
    {
        $this->storeRecord(["title" => "Bracket"], UserType::MEMBER)
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

        return $this->postJson(route("brackets.store"), $data);
    }
}
