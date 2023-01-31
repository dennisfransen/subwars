<?php

namespace Tests\Feature;

use App\Http\Enums\SupportTicketPriority;
use App\Http\Enums\SupportTicketType;
use App\Http\Enums\UserType;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class SupportTicketUpdateTest extends ApiTestCase
{
    private $record, $data, $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = SupportTicket::factory()->create([
            "sender_id" => User::factory()->create()->id,
        ]);

        $this->data = [];

        $this->user = User::factory()->create(["type" => UserType::SUPERADMIN]);
    }

    public function testUpdatesDescription()
    {
        $this->updateRecord(["description" => "New description"], $this->user);

        $this->assertEquals("New description", SupportTicket::first()->description);
    }

    public function testUpdatesType()
    {
        $this->updateRecord(["type" => SupportTicketType::MISSING_PRICE], $this->user);

        $this->assertEquals(SupportTicketType::MISSING_PRICE, SupportTicket::first()->type);
    }

    public function testUpdatesPriority()
    {
        $this->updateRecord(["priority" => SupportTicketPriority::STREAMER], $this->user);

        $this->assertEquals(SupportTicketPriority::STREAMER, SupportTicket::first()->priority);
    }

    public function testUpdatesEmail()
    {
        $this->updateRecord(["email" => "new_test@gmail.com"], $this->user);

        $this->assertEquals("new_test@gmail.com", SupportTicket::first()->email);
    }

    public function testReturnsHttpOk()
    {
        $this->updateRecord($this->data, $this->user)->assertStatus(200);
    }

    public function testFailsOnRecordNotFound()
    {
        $this->record->delete();

        $this->updateRecord([], $this->user)
            ->assertStatus(404);
    }

    public function testFailsOnBadType()
    {
        $this->updateRecordAndAssertFail(["type" => 5]);
        $this->updateRecordAndAssertFail(["type" => null]);
    }

    public function testFailsOnBadPriority()
    {
        $this->updateRecordAndAssertFail(["priority" => 5]);
        $this->updateRecordAndAssertFail(["priority" => null]);
    }

    public function testFailsOnBadDescription()
    {
        $this->updateRecordAndAssertFail(["description" => null]);
        $this->updateRecordAndAssertFail(["description" => "I"]);
    }

    public function testFailsOnBadEmail()
    {
        $this->updateRecordAndAssertFail(["email" => null]);
        $this->updateRecordAndAssertFail(["email" => "bad"]);
    }

    /**
     * @param array $data
     */
    private function updateRecordAndAssertFail(array $data)
    {
        $this->updateRecord($data, $this->user)
            ->assertStatus(422);
    }

    public function testGuestReturnsUnauthorized()
    {
        $this->updateRecord($this->data, null)
            ->assertStatus(401);
    }

    public function testConfirmedCantUpdateAny()
    {
        $this->updateRecord($this->data, User::factory()->create(["type" => UserType::STREAMER]))
            ->assertStatus(403);
    }

    /**
     * @param array $data
     * @param User|null $user
     * @return TestResponse
     */
    private function updateRecord(array $data = null, User $user = null): TestResponse
    {
        if ($data)
            $data = array_merge($this->data, $data);
        else
            $data = $this->data;

        if ($user !== null)
            $this->actingAs($user);

        return $this->putJson(route("support_tickets.update", $this->record), $data);
    }
}
