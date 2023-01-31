<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class UserEsportalTest extends ApiTestCase
{
    private $user, $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->data = [
            "esportal_username" => "MrMotoX",
        ];
    }

    public function testUsernameDuplicateInDatabaseReturnsError()
    {
        User::factory()->create($this->data);

        $this->updateUser($this->user)
            ->assertStatus(422);
    }

    public function testUsernameDoesNotExistInEsportalReturnsError()
    {
        $this->data["esportal_username"] = "verybadusernamethatdoesntexist";

        $this->updateUser($this->user)
            ->assertStatus(422);
    }

    public function testUsernameIsUpdated()
    {
        $this->updateUser($this->user)
            ->assertStatus(200);

        $this->user->refresh();
        $this->assertEquals($this->data["esportal_username"], $this->user->esportal_username);
    }

    /**
     * @param User|null $user
     * @return TestResponse
     */
    private function updateUser(User $user = null): TestResponse
    {
        if ($user)
            $this->actingAs($user);

        return $this->putJson(route("users.update", $this->user), $this->data);
    }
}
