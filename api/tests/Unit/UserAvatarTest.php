<?php

namespace Tests\Unit;

use App\Models\LinkedSocialAccount;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Tests\TestCase;

class UserAvatarTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = User::factory()->create();

        LinkedSocialAccount::factory()->create([
            "user_id" => $this->record->id,
            "avatar" => "http://avatar.jpg",
        ]);
    }

    public function testItHasAvatar()
    {
        $this->assertEquals("http://avatar.jpg", $this->record->avatar);
    }

    public function testItHasMediumAvatar()
    {
        $this->assertEquals("http://avatar_medium.jpg", $this->record->getAvatarWithSuffix("_medium"));
    }

    public function testItHasFullAvatar()
    {
        $this->assertEquals("http://avatar_full.jpg", $this->record->getAvatarWithSuffix("_full"));
    }
}
