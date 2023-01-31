<?php

namespace Tests\Unit;

use App\Http\Enums\TournamentUserState;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Tests\TestCase;

class TournamentTeamsTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
    }

    public function testItHasTeams()
    {
        Team::factory()->count(2)->create([
            "tournament_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(Team::class, $this->record->teams()->first());
        $this->assertCount(2, $this->record->teams);
    }

    public function testItScramblesReserveFromScratch()
    {
        $this->createAndAttachPlayers(14);

        $attachedPlayers = $this->record->scrambleTeamsGetAttachedPlayerCount();
        $this->record->refresh();

        $this->assertEquals(10, $attachedPlayers);
        $this->assertCount(2, $this->record->teams);
        $this->assertCount(5, $this->record->teams[0]->users);
        $this->assertCount(5, $this->record->teams[1]->users);
    }

    public function testItScramblesReserveWithExistingFullTeam()
    {
        $this->createAndAttachPlayers(14);
        $this->createTeamAndAttachPlayers(5);

        $attachedPlayers = $this->record->scrambleTeamsGetAttachedPlayerCount();
        $this->record->refresh();

        $this->assertEquals(10, $attachedPlayers);
        $this->assertCount(2, $this->record->teams);
        $this->assertCount(5, $this->record->teams[0]->users);
        $this->assertCount(5, $this->record->teams[1]->users);
    }

    public function testItScramblesReserveWithExistingHalfTeam()
    {
        $this->createAndAttachPlayers(14);
        $this->createTeamAndAttachPlayers(3);

        $attachedPlayers = $this->record->scrambleTeamsGetAttachedPlayerCount();
        $this->record->refresh();

        $this->assertEquals(10, $attachedPlayers);
        $this->assertCount(2, $this->record->teams);
        $this->assertCount(5, $this->record->teams[0]->users);
        $this->assertCount(5, $this->record->teams[1]->users);
    }

    public function testItAbortsScrambleReserveWithTooSmallReserve()
    {
        $this->createAndAttachPlayers(4);
        $this->createTeamAndAttachPlayers(3);

        $attachedPlayers = $this->record->scrambleTeamsGetAttachedPlayerCount();
        $this->record->refresh();

        $this->assertEquals(0, $attachedPlayers);
        $this->assertCount(0, $this->record->teams);
    }

    public function testReserveExcludesPlayersInTeams()
    {
        $teams = Team::factory()->count(1)->create([
            "tournament_id" => $this->record->id,
        ]);
        $this->record->users()->attach(User::factory()->count(5)->create(), [
            "state" => TournamentUserState::CHECKED_IN,
        ]);
        $teams->first()->users()->attach([
            $this->record->users[0]->id,
            $this->record->users[1]->id,
        ]);

        $this->assertCount(3, $this->record->reserve);
    }

    public function testReserveExcludesPlayersInTeamsAndAccountsForMaxPlayers()
    {
        $this->record->max_teams = 1;
        $this->record->save();
        $teams = Team::factory()->count(1)->create([
            "tournament_id" => $this->record->id,
        ]);
        $this->record->users()->attach(User::factory()->count(10)->create(), [
            "state" => TournamentUserState::CHECKED_IN,
        ]);
        $teams->first()->users()->attach([
            $this->record->users[0]->id,
            $this->record->users[1]->id,
        ]);

        $this->assertCount(3, $this->record->reserve);
    }

    /**
     * @param int $count
     */
    private function createAndAttachPlayers(int $count): void
    {
        $users = User::factory()->count($count)->create();
        $this->record->users()->attach($users->pluck("id"), [
            "state" => TournamentUserState::CHECKED_IN,
        ]);
    }

    /**
     * @param int $count
     */
    private function createTeamAndAttachPlayers(int $count): void
    {
        $team = Team::factory()->create([
            "tournament_id" => $this->record->id,
        ]);
        $team->users()->attach($this->record->reserve()->limit($count)->pluck("id"));
    }
}
