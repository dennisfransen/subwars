<?php

namespace Tests\Unit;

use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\User;
use Tests\TestCase;

class SponsorTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Sponsor::factory()->create();
    }

    public function testItHasTournaments()
    {
        $this->record->tournaments()->attach(Tournament::factory()->count(2)->create());

        $this->assertInstanceOf(Tournament::class, $this->record->tournaments()->first());
        $this->assertCount(2, $this->record->tournaments);
    }

    public function testItHasUsers() {
        $this->record->users()->attach(User::factory()->count(2)->create());

        $this->assertInstanceOf(User::class, $this->record->users()->first());
        $this->assertCount(2, $this->record->users);
    }
}
