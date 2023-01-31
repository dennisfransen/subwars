<?php

namespace Tests\Unit;

use App\Models\SupportTicket;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = SupportTicket::factory()->create();
    }

    public function testItHasResponder() {
        $this->record->responder_id = User::factory()->create()->id;
        $this->record->save();

        $this->assertInstanceOf(User::class, $this->record->responder);
    }

    public function testItHasSender()
    {
        $this->record->sender_id = User::factory()->create()->id;
        $this->record->save();

        $this->assertInstanceOf(User::class, $this->record->sender);
    }

    public function testItHasUnreadAttribute() {
        $this->record->read_at = null;
        $this->record->save();

        $this->assertTrue($this->record->unread);

        $this->record->read_at = Carbon::now()->addDay();
        $this->record->save();

        $this->assertFalse($this->record->unread);
    }
}
