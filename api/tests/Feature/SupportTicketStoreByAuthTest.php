<?php

namespace Tests\Feature;

use App\Http\Enums\SupportTicketType;
use App\Http\Enums\UserType;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class SupportTicketStoreByAuthTest extends ApiTestCase
{
    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data["description"] = "Support ticket description";
    }

    public function testStoresDescription()
    {
        $this->storeRecord();

        $this->assertEquals($this->data["description"], SupportTicket::first()->description);
    }

    public function testStoresAdditionalInformation()
    {
        $this->data["type"] = SupportTicketType::BANNED;
        $this->storeRecord();

        $record = SupportTicket::first();
        $this->assertEquals($this->data["type"], $record->type);
    }

    public function testStoresSenderId() {
        $this->storeRecord();

        $this->assertEquals(1, SupportTicket::first()->sender_id);
    }

    public function testReturnsHttpCreated()
    {
        $this->storeRecord()->assertStatus(201);
    }

    public function testFailsWithoutDescription()
    {
        unset($this->data["description"]);

        $this->storeRecordAndAssertFail([]);
    }

    public function testFailsOnTooShortDescription()
    {
        $this->storeRecordAndAssertFail(["description" => "I"]);
    }

    public function testFailsOnBadType()
    {
        $this->storeRecordAndAssertFail(["type" => 5]);
    }

    public function testGuestReturnsUnauthorized() {
        $this->storeRecord(null, null)
            ->assertStatus(401);
    }

    /**
     * @param array $data
     */
    private function storeRecordAndAssertFail(array $data)
    {
        $this->storeRecord($data)
            ->assertStatus(422);
    }

    /**
     * @param array|null $data
     * @param int|null $userType
     * @return TestResponse
     */
    private function storeRecord(array $data = null, ?int $userType = UserType::GUEST): TestResponse
    {
        if ($data)
            $data = array_merge($this->data, $data);
        else
            $data = $this->data;

        if ($userType !== null)
            $this->actingAs(User::factory()->create(["type" => $userType]));

        return $this->postJson(route("support_tickets.store_by_auth"), $data);
    }
}
