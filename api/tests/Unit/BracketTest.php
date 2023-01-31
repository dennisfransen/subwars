<?php

namespace Tests\Unit;

use App\Http\Enums\ReleaseStatus;
use App\Models\Bracket;
use App\Models\Tournament;
use Tests\TestCase;

class BracketTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Bracket::factory()->create();
    }

    public function testItHasTournaments()
    {
        Tournament::factory()->count(2)->create([
            "bracket_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(Tournament::class, $this->record->tournaments()->first());
        $this->assertCount(2, $this->record->tournaments);
    }

    public function testItHasStatusComingSoonWhenNew() {
        $this->assertEquals(ReleaseStatus::COMING_SOON, $this->record->status);
    }
}
