<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\ApiTestCase;

class RegisterTest extends ApiTestCase
{
    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        Client::factory()->create();

        $this->data = [
            "username" => "Admin",
            "esportal_username" => "MrMotoX",
            "password" => "password",
        ];
    }

    public function testSuccess()
    {
        $response = $this->register();

        $response->assertStatus(201);

        $user = User::first();

        $this->assertCount(1, User::all());
        $this->assertEquals("Admin", $user->username);
        $this->assertEquals("MrMotoX", $user->esportal_username);
        $this->assertEquals(1200, $user->esportal_elo);
    }

    public function testFailureOnBadEsportalUsername()
    {
        $this->registerAndAssertFail(["esportal_username" => "really_bad_name_that_does_not_exist"]);
        $this->registerAndAssertFail(["esportal_username" => null]);
    }

    public function testFailureOnDuplicateEsportalUsername()
    {
        User::factory()->create(["esportal_username" => "John"]);

        $this->registerAndAssertFail(["esportal_username" => "John"]);
    }

    public function testFailureOnBadUsername()
    {
        $this->registerAndAssertFail(["username" => null]);
    }

    public function testFailureOnDuplicateUsername()
    {
        User::factory()->create(["username" => "John"]);

        $this->registerAndAssertFail(["username" => "John"]);
    }

    /**
     * @param array $data
     */
    private function registerAndAssertFail(array $data)
    {
        $this->register($data)
            ->assertStatus(422);
    }

    /**
     * @param array|null $data
     * @return TestResponse
     */
    private function register(array $data = null): TestResponse
    {
        if ($data)
            $data = array_merge($this->data, $data);
        else
            $data = $this->data;

        return $this->postJson(route("auth.register"), $data);
    }
}
