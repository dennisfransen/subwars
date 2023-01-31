<?php

namespace Tests\Unit;

use App\Models\Price;
use App\Models\Tournament;
use Tests\TestCase;

class PriceTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Price::factory()->create();
    }

    public function testItHasTournament()
    {
        $this->assertInstanceOf(Tournament::class, $this->record->tournament);
    }
}
