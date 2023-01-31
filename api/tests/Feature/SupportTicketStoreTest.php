<?php

namespace Tests\Feature;

use App\Http\Enums\SupportTicketType;
use App\Models\SupportTicket;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class SupportTicketStoreTest extends ApiTestCase
{
    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data["description"] = "Support ticket description";
        $this->data["email"] = "test@gmail.com";
    }

    public function testStoresDescription()
    {
        $this->storeRecord();

        $this->assertEquals($this->data["description"], SupportTicket::first()->description);
        $this->assertEquals($this->data["email"], SupportTicket::first()->email);
    }

    public function testStoresWithoutSenderId()
    {
        $this->storeRecord();

        $this->assertNull(SupportTicket::first()->sender_id);
    }

    public function testStoresAdditionalInformation()
    {
        $this->data["type"] = SupportTicketType::BANNED;
        $this->storeRecord();

        $record = SupportTicket::first();
        $this->assertEquals($this->data["type"], $record->type);
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

    public function testFailsOnBadEmail()
    {
        $this->storeRecordAndAssertFail(["email" => null]);
        $this->storeRecordAndAssertFail(["email" => "bad"]);
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
     * @return TestResponse
     */
    private function storeRecord(array $data = null): TestResponse
    {
        if ($data)
            $data = array_merge($this->data, $data);
        else
            $data = $this->data;

        return $this->postJson(route("support_tickets.store"), $data);
    }
}
