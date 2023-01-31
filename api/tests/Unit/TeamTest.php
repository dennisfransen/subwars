<?php

namespace Tests\Unit;

use App\Events\MultiChannelEvent;
use App\Events\PlayerDetachedFromTeamEvent;
use App\Events\TeamDestroyedEvent;
use App\Events\TeamUpdatedEvent;
use App\Http\Enums\TournamentUserState;
use App\Models\Fight;
use App\Models\Notification;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Event;
use Tests\TestCase;

class TeamTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Team::factory()->create();
    }

    /* Relationships */

    public function testItHasFights()
    {
        $this->record
            ->fights()
            ->attach(Fight::factory()
                ->count(2)
                ->create(["tournament_id" => $this->record->tournament_id])
                ->pluck("id")
            );

        $this->assertInstanceOf(Fight::class, $this->record->fights()->first());
        $this->assertCount(2, $this->record->fights);
    }

    public function testItHasTournament()
    {
        $this->assertInstanceOf(Tournament::class, $this->record->tournament);
    }

    public function testItHasNotifications()
    {
        Notification::factory()->count(2)->create([
            "notifiable_type" => Team::class,
            "notifiable_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(Notification::class, $this->record->notifications()->first());
        $this->assertCount(2, $this->record->notifications);
    }

    public function testItHasUsers()
    {
        $this->record->users()->attach(User::factory()->count(2)->create());

        $this->assertInstanceOf(User::class, $this->record->users()->first());
        $this->assertCount(2, $this->record->users);
    }

    /* Attributes */

    public function testItHasAverageEloOfExistingUsers()
    {
        $this->record->users()->attach(User::factory()->create(["esportal_elo" => 1000]));
        $this->record->users()->attach(User::factory()->create(["esportal_elo" => 3000]));
        $this->record->users()->attach(User::factory()->create(["esportal_elo" => 2000]));
        $this->record->users()->attach(User::factory()->create(["esportal_elo" => 6000]));
        $this->record->users()->attach(User::factory()->create(["esportal_elo" => 5000]));

        $this->assertEquals(3400, $this->record->averageElo);
    }

    public function testItHasAverageEloOfZeroUsers()
    {
        $this->assertEquals(0, $this->record->averageElo);
    }

    public function testItHasOptimalEloAccordingToTargetOnZeroPlayers()
    {
        $expectedOptimalElo = 1000;

        $this->assertEquals($expectedOptimalElo, $this->record->getOptimalEloToTarget($expectedOptimalElo));
    }

    public function testItHasOptimalEloWithOnePlayer()
    {
        $user = User::factory()->create([
            "esportal_elo" => 2000,
        ]);
        $this->record->tournament->users()->attach($user, [
            "state" => TournamentUserState::CHECKED_IN,
        ]);
        $this->record->users()->attach($user);

        $this->assertEquals(4000, $this->record->getOptimalEloToTarget(3000));
    }

    public function testItHasOptimalEloWithTwoPlayers()
    {
        $user1 = User::factory()->create([
            "esportal_elo" => 2000,
        ]);
        $user2 = User::factory()->create([
            "esportal_elo" => 4000,
        ]);
        $this->record->tournament->users()->attach([$user1->id, $user2->id], [
            "state" => TournamentUserState::CHECKED_IN,
        ]);
        $this->record->users()->attach([$user1->id, $user2->id]);

        $this->assertEquals(18000, $this->record->getOptimalEloToTarget(8000));
    }

    public function testItHasOptimalEloWithFourPlayers()
    {
        for ($i = 0; $i <= 3; $i++) {
            $user = User::factory()->create([
                "esportal_elo" => ($i + 1) * 1000,
            ]);
            $this->record->tournament->users()->attach($user, [
                "state" => TournamentUserState::CHECKED_IN,
            ]);
            $this->record->users()->attach($user);
        }

        $this->assertEquals(7500, $this->record->getOptimalEloToTarget(3500));
    }

    public function testItHasOptimalEloOfMinusOneWithFullTeam()
    {
        for ($i = 0; $i < Tournament::TEAM_PLAYER_COUNT; $i++) {
            $user = User::factory()->create();
            $this->record->tournament->users()->attach($user, [
                "state" => TournamentUserState::CHECKED_IN,
            ]);
            $this->record->users()->attach($user);
        }

        $this->assertEquals(-1, $this->record->getOptimalEloToTarget(2000));
    }

    /* Events */

    public function testItDispatchesTeamUpdatedEvent()
    {
        Event::fake();

        $this->record->dispatchTeamUpdatedEvent();

        Event::assertDispatchedTimes(TeamUpdatedEvent::class);
    }

    public function testItDispatchesTeamDestroyedEvent()
    {
        Event::fake();

        $this->record->dispatchTeamDestroyedEvent();

        Event::assertDispatchedTimes(TeamDestroyedEvent::class);
    }

    public function testItDispatchesPlayerDetachedFromTeamEvent()
    {
        Event::fake();

        $this->record->dispatchPlayerDetachedFromTeamEvent(1,
            MultiChannelEvent::CHANNEL_TOURNAMENT,
            $this->record->tournament_id);

        Event::assertDispatchedTimes(PlayerDetachedFromTeamEvent::class);
    }

    public function testItDestroysNotificationsUponDeletion()
    {
        $user = User::factory()->create();
        $otherTeam = Team::factory()->create();
        $user->createNotificationWithModelAndDescription($this->record, "Description");
        $user->createNotificationWithModelAndDescription($this->record, "Description");
        $user->createNotificationWithModelAndDescription($otherTeam, "Description");

        $this->assertCount(3, $user->notifications()->get());

        $this->record->delete();

        $this->assertCount(1, $user->notifications()->get());
        $this->assertEquals($otherTeam->id, $user->notifications()->first()->notifiable_id);
    }

    /* Helpers */

    public function testItAttachesPlayer()
    {
        $user = User::factory()->create();
        $this->assertCount(0, $this->record->users()->where("users.id", $user->id)->get());

        $this->record->attachPlayer($user);

        $this->assertCount(1, $this->record->users()->where("users.id", $user->id)->get());
    }

    public function testAttachesPlayerCreatesNotification()
    {
        $user = User::factory()->create();

        $this->record->attachPlayer($user);

        $notification = $user->notifications()->first();
        $this->assertEquals(Team::class, $notification->notifiable_type);
        $this->assertEquals($this->record->id, $notification->notifiable_id);
        $this->assertEquals("You were assigned a team.", $notification->description);
        $this->assertEquals($user->id, $notification->user_id);
    }

    public function testItDetachesPlayer()
    {
        $user = User::factory()->create();
        $this->record->users()->attach($user);
        $this->assertCount(1, $this->record->users()->where("users.id", $user->id)->get());

        $this->record->detachPlayer($user);

        $this->assertCount(0, $this->record->users()->where("users.id", $user->id)->get());
    }

    public function testDetachesPlayerCreatesNotification()
    {
        $user = User::factory()->create();
        $this->record->users()->attach($user);

        $this->record->detachPlayer($user);

        $notification = $user->notifications()->first();
        $this->assertEquals(Team::class, $notification->notifiable_type);
        $this->assertEquals($this->record->id, $notification->notifiable_id);
        $this->assertEquals("You were removed from a team.", $notification->description);
        $this->assertEquals($user->id, $notification->user_id);
    }
}
