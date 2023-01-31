<?php

namespace Tests\Unit;

use App\Models\LinkedSocialAccount;
use App\Models\User;
use Tests\TestCase;

class LinkedSocialAccountTest extends TestCase
{
    public function testItHasUser()
    {
        $linkedSocialAccount = LinkedSocialAccount::factory()->create();

        $this->assertInstanceOf(User::class, $linkedSocialAccount->user);
    }
}
