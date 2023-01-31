<?php

namespace Tests\Unit;

use App\Http\Enums\TournamentUserState;
use App\Models\TeamUser;
use App\Models\Tournament;
use App\Models\User;
use Tests\TestCase;

class TournamentScramblePlayersToTeamsTest extends TestCase
{
    private $record, $users;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();

        $this->users[] = $this->attachUserWithElo(1000);
        $this->users[] = $this->attachUserWithElo(900);
        $this->users[] = $this->attachUserWithElo(1100);
        $this->users[] = $this->attachUserWithElo(1050);
        $this->users[] = $this->attachUserWithElo(910);

        $this->users[] = $this->attachUserWithElo(980);
        $this->users[] = $this->attachUserWithElo(970);
        $this->users[] = $this->attachUserWithElo(1150);
        $this->users[] = $this->attachUserWithElo(1080);
        $this->users[] = $this->attachUserWithElo(920);

        $this->users[] = $this->attachUserWithElo(1250);
        $this->users[] = $this->attachUserWithElo(1020);
    }

    public function testItScramblesCheckedInUsersOnly()
    {
        for ($i = 0; $i <= 10; $i++) {
            $this->attachUserWithElo(rand(900, 1200), TournamentUserState::CHECKED_IN_KICKED);
            $this->attachUserWithElo(rand(900, 1200), TournamentUserState::REGISTERED_KICKED);
            $this->attachUserWithElo(rand(900, 1200), TournamentUserState::REGISTERED);
        }

        $this->record->scrambleTeamsGetAttachedPlayerCount();

        $teamIds = $this->record->teams()->pluck("id");
        $playerIds = TeamUser::whereIn("team_id", $teamIds)
            ->pluck("user_id");

        $this->assertCount(10, $playerIds);
        $this->assertCount(10, $this->record
            ->users()
            ->whereIn("users.id", Collect($this->users)->take(10)->pluck("id"))
            ->whereIn("users.id", $playerIds)
            ->get());
        $this->assertEquals(2, $this->record->teams()->count());
    }

    /**
     * @param int $elo
     * @param int $state
     * @return User
     */
    private function attachUserWithElo(int $elo, int $state = TournamentUserState::CHECKED_IN): User
    {
        $user = User::factory()->create([
            "esportal_elo" => $elo,
        ]);

        $this->record->users()->attach($user, [
            "state" => $state,
        ]);

        return $user;
    }
}
