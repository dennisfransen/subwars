<?php

namespace Tests\Unit;

use App\Http\Enums\FightTeamResult;
use App\Models\Fight;
use App\Models\Team;
use App\Models\Tournament;
use Tests\TestCase;

class FightTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Fight::factory()->create();
    }

    public function testItHasChild()
    {
        $this->record->child_id = Fight::factory()->create()->id;
        $this->record->save();

        $this->assertInstanceOf(Fight::class, $this->record->child);
    }

    public function testItHasParents()
    {
        Fight::factory()->count(2)->create([
            "child_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(Fight::class, $this->record->parents()->first());
        $this->assertCount(2, $this->record->parents);
    }

    public function testItHasTeams()
    {
        $this->record->teams()->attach(Team::factory()->count(2)->create()->pluck("id"));

        $this->assertInstanceOf(Team::class, $this->record->teams()->first());
        $this->assertCount(2, $this->record->teams);
    }

    public function testItHasTournament()
    {
        $this->assertInstanceOf(Tournament::class, $this->record->tournament);
    }

    public function testItHasWinners()
    {
        $teams = Team::factory()->count(2)->create([
            "tournament_id" => $this->record->tournament_id,
        ]);
        $this->record->teams()->attach($teams[0], ["result" => FightTeamResult::LOSS]);
        $this->record->teams()->attach($teams[1], ["result" => FightTeamResult::WIN]);

        $this->assertInstanceOf(Team::class, $this->record->winners()->first());
        $this->assertEquals($teams[1]->id, $this->record->winners[0]->id);
        $this->assertCount(1, $this->record->winners);
    }

    public function testItHasLosers()
    {
        $teams = Team::factory()->count(2)->create([
            "tournament_id" => $this->record->tournament_id,
        ]);
        $this->record->teams()->attach($teams[0], ["result" => FightTeamResult::WIN]);
        $this->record->teams()->attach($teams[1], ["result" => FightTeamResult::LOSS]);

        $this->assertInstanceOf(Team::class, $this->record->losers()->first());
        $this->assertEquals($teams[1]->id, $this->record->losers[0]->id);
        $this->assertCount(1, $this->record->losers);
    }
}
