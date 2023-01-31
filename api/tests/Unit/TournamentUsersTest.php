<?php

namespace Tests\Unit;

use App\Http\Enums\TournamentUserState;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Tests\TestCase;

class TournamentUsersTest extends TestCase
{
    private $record, $registered, $checkedIn, $registeredKicked, $checkedInKicked;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
        $this->registered[] = $this->attachNewUser(TournamentUserState::REGISTERED);
        $this->registered[] = $this->attachNewUser(TournamentUserState::REGISTERED);
        $this->registered[] = $this->attachNewUser(TournamentUserState::REGISTERED);
        $this->checkedIn[] = $this->attachNewUser(TournamentUserState::CHECKED_IN, 1000);
        $this->checkedIn[] = $this->attachNewUser(TournamentUserState::CHECKED_IN, 1100);
        $this->checkedIn[] = $this->attachNewUser(TournamentUserState::CHECKED_IN, 1060);
        $this->registeredKicked[] = $this->attachNewUser(TournamentUserState::REGISTERED_KICKED);
        $this->registeredKicked[] = $this->attachNewUser(TournamentUserState::REGISTERED_KICKED);
        $this->checkedInKicked[] = $this->attachNewUser(TournamentUserState::CHECKED_IN_KICKED);
        $this->checkedInKicked[] = $this->attachNewUser(TournamentUserState::CHECKED_IN_KICKED);
    }

    public function testItHasRegistered()
    {
        $this->assertInstanceOf(User::class, $this->record->registered()->first());
    }

    public function testItHasRegisteredOfRegisteredAndCheckedInOnly()
    {
        $this->assertCount(6, $this->record->registered);
        for ($i = 0; $i <= 2; $i++) {
            $this->assertEquals($this->registered[$i]->id, $this->record->registered[$i]->id);
        }
        for ($i = 0; $i <= 2; $i++) {
            $this->assertEquals($this->checkedIn[$i]->id, $this->record->registered[$i + 3]->id);
        }
    }

    public function testItHasCheckedIn()
    {
        $this->assertInstanceOf(User::class, $this->record->checkedIn()->first());
    }

    public function testItHasCheckedInOfCheckedInOnly()
    {
        $this->assertCount(3, $this->record->checkedIn);
        for ($i = 0; $i <= 2; $i++) {
            $this->assertEquals($this->checkedIn[$i]->id, $this->record->checkedIn[$i]->id);
        }
    }

    public function testItHasReserve()
    {
        $this->assertInstanceOf(User::class, $this->record->reserve()->first());
    }

    public function testItHasReserveOfCheckedInOnly()
    {
        $this->assertCount(3, $this->record->reserve);
        for ($i = 0; $i <= 2; $i++) {
            $this->assertEquals($this->checkedIn[$i]->id, $this->record->reserve[$i]->id);
        }
    }

    public function testReserveIsLimitedByMaxPlayers()
    {
        for ($i = 0; $i <= 10; $i++) {
            $this->attachNewUser(TournamentUserState::CHECKED_IN);
        }
        $this->record->max_teams = 2;
        $this->record->save();

        $this->assertCount(14, $this->record->checkedIn);
        $this->assertCount(10, $this->record->reserve);
    }

    public function testItHasAverageEloOfExistingReserve()
    {
        $expectedAverageElo = (int)collect($this->checkedIn)->average("esportal_elo");

        $this->assertEquals($expectedAverageElo, $this->record->averageEloReserve);
    }

    public function testItHasAverageOfZeroReserve()
    {
        $this->assertEquals(0, Tournament::factory()->create()->averageEloReserve);
    }

    public function testItHasMaxPlayers()
    {
        $this->record->max_teams = 5;
        $this->record->save();

        $this->assertEquals(25, $this->record->maxPlayers);
    }

    public function testItHasUnlimitedMaxPlayers()
    {
        $this->record->max_teams = -1;
        $this->record->save();

        $this->assertEquals(-1, $this->record->maxPlayers);
    }

    public function testItHasEvenPlayerCountByReserveOnly()
    {
        for ($i = 0; $i <= 10; $i++) {
            $this->attachNewUser(TournamentUserState::CHECKED_IN);
        }

        $this->assertEquals(10, $this->record->evenPlayerCount);
    }

    public function testItHasEvenPlayerCountByReserveAndPlayers()
    {
        for ($i = 0; $i <= 10; $i++) {
            $this->attachNewUser(TournamentUserState::CHECKED_IN);
        }

        Team::factory()->create([
            "tournament_id" => $this->record->id,
        ]);
        $this->record->teams[0]->users()->attach($this->record->reserve()->limit(5)->pluck("id"));

        $this->assertEquals(10, $this->record->evenPlayerCount);
    }

    public function testItGetsReservePlayerClosestToElo()
    {
        $this->attachNewUser(TournamentUserState::CHECKED_IN, 2000);
        $this->attachNewUser(TournamentUserState::CHECKED_IN, 2020);
        $this->attachNewUser(TournamentUserState::CHECKED_IN, 2080);
        $this->attachNewUser(TournamentUserState::CHECKED_IN, 2090);
        $this->attachNewUser(TournamentUserState::CHECKED_IN, 2200);

        $this->assertInstanceOf(User::class, $this->record->getReservePlayerClosestToElo(2070));
        $this->assertEquals(2080, $this->record->getReservePlayerClosestToElo(2070)->esportal_elo);
        $this->assertEquals(2080, $this->record->getReservePlayerClosestToElo(2084)->esportal_elo);
        $this->assertEquals(2090, $this->record->getReservePlayerClosestToElo(2086)->esportal_elo);
        $this->assertEquals(2200, $this->record->getReservePlayerClosestToElo(2300)->esportal_elo);
        $this->assertEquals(1000, $this->record->getReservePlayerClosestToElo(900)->esportal_elo);
    }

    public function testItGetsNullReservePlayerClosestToEloOnEmptyReserve()
    {
        $this->record->reserve()->sync([]);

        $this->assertNull($this->record->getReservePlayerClosestToElo(1000));
    }

    /**
     * @param int $state
     * @return User
     */
    private function attachNewUser(int $state, int $elo = null): User
    {
        if ($elo)
            $user = User::factory()->create(["esportal_elo" => $elo]);
        else
            $user = User::factory()->create();

        $this->record->users()->attach($user, [
            "state" => $state,
        ]);

        return $user;
    }
}
