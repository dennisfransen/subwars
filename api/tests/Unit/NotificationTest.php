<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\Tournament;
use App\Models\User;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Notification::factory()->create();
    }

    /* Relationships */

    public function testItHasNotifiableAsTournament()
    {
        $notification = Notification::factory()->create([
            "notifiable_type" => Tournament::class,
            "notifiable_id" => Tournament::factory()->create()->id,
        ]);

        $this->assertInstanceOf(Tournament::class, $notification->notifiable);
    }

    public function testItHasUser()
    {
        $this->assertInstanceOf(User::class, $this->record->user);
    }
}
