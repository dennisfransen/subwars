<?php

namespace Tests\Feature;

use App\Http\Enums\UserType;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class NotificationIndexTest extends ApiTestCase
{
    private $records;

    protected function setUp(): void
    {
        parent::setUp();

        $this->records = Notification::factory()->count(2)->create();
    }

    public function testReturnsAllRecords()
    {
        $response = $this->indexRecords(null);

        $this->assertCount(2, $response->json("data"));
        $this->assertEquals(
            $this->records->pluck("id"),
            $this->getJsonCollection($response)->pluck("id")
        );
    }

    public function testReturnsByUserOnly()
    {
        $response = $this->indexRecords(["user_id" => $this->records[0]->user_id]);

        $this->assertCount(1, $response->json("data"));
        $this->assertEquals(
            Notification::where("user_id", $this->records[0]->user_id)->pluck("id"),
            $this->getJsonCollection($response)->pluck("id")
        );
    }

    public function testUserCanNotIndexByAnyUser()
    {
        $this->indexRecords([], $this->records[1]->user)
            ->assertStatus(403);
    }

    public function testGuestGanNotIndex()
    {
        $this->getJson(route("notifications.index"))
            ->assertStatus(401);
    }

    public function testUserCanIndexByTheirOwnUser()
    {
        $this->indexRecords(["user_id" => $this->records[0]->user_id], $this->records[0]->user)
            ->assertStatus(200);
    }

    /**
     * @param int|null $parameters
     * @param int|null $userType
     * @return TestResponse
     */
    private function indexRecords(?array $parameters, User $user = null): TestResponse
    {
        if ($user !== null)
            $this->actingAs($user);
        else
            $this->actingAs(User::factory()->create(["type" => UserType::SUPERADMIN]));

        return $this->getJson(route("notifications.index", $parameters));
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
