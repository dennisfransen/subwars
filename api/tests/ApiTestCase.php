<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Tests\TestCase as BaseTestCase;

abstract class ApiTestCase extends BaseTestCase
{
    /**
     * @param UserContract $user
     * @param string $guard
     * @return ApiTestCase
     */
    public function actingAs(UserContract $user, $guard = "api"): ApiTestCase
    {
        return parent::actingAs($user, $guard);
    }
}
