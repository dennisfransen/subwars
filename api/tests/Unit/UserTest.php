<?php

namespace Tests\Unit;

use App\Events\PlayerDetachedFromTeamEvent;
use App\Events\TeamDestroyedEvent;
use App\Events\TournamentOpenedForCheckInEvent;
use App\Events\UserDeRegisteredFromTournamentEvent;
use App\Http\Enums\CasterRole;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\LinkedSocialAccount;
use App\Models\Notification;
use App\Models\Sponsor;
use App\Models\SupportTicket;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Symfony\Component\VarDumper\Caster\Caster;
use Tests\TestCase;

class UserTest extends TestCase
{
    private $record, $defaultAvatar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = User::factory()->create();
        $this->defaultAvatar = "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg";
    }

    /* Relations */

    public function testItHasCoCastedTournaments()
    {
        $this->record->coCastedTournaments()->attach(Tournament::factory()->count(2)->create()->pluck("id"), [
            "role" => CasterRole::CO_CASTER,
        ]);

        $this->assertInstanceOf(Tournament::class, $this->record->coCastedTournaments()->first());
        $this->assertCount(2, $this->record->coCastedTournaments);
    }

    public function testItHasMainCastedTournaments()
    {
        $this->record->mainCastedTournaments()->attach(Tournament::factory()->count(2)->create()->pluck("id"), [
            "role" => CasterRole::MAIN_CASTER,
        ]);

        $this->assertInstanceOf(Tournament::class, $this->record->mainCastedTournaments()->first());
        $this->assertCount(2, $this->record->mainCastedTournaments);
    }

    public function testItHasLinkedSocialAccounts()
    {
        LinkedSocialAccount::factory()->count(2)->create([
            "user_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(
            LinkedSocialAccount::class,
            $this->record->linkedSocialAccounts()->first()
        );
        $this->assertCount(2, $this->record->linkedSocialAccounts);
    }

    public function testItHasOwnedTournaments()
    {
        Tournament::factory()->count(2)->create([
            "user_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(
            Tournament::class,
            $this->record->ownedTournaments()->first()
        );
        $this->assertCount(2, $this->record->ownedTournaments);
    }

    public function testItHasNotifications()
    {
        Notification::factory()->count(2)->create([
            "user_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(Notification::class, $this->record->notifications()->first());
        $this->assertCount(2, $this->record->notifications);
    }

    public function testItHasTeams()
    {
        $this->record->teams()->attach(Team::factory()->count(2)->create());

        $this->assertInstanceOf(Team::class, $this->record->teams()->first());
        $this->assertCount(2, $this->record->teams);
    }

    public function testItHasSponsors()
    {
        $this->record->sponsors()->attach(Sponsor::factory()->count(2)->create());

        $this->assertInstanceOf(Sponsor::class, $this->record->sponsors()->first());
        $this->assertCount(2, $this->record->sponsors);
    }

    public function testItHasSupportTickets()
    {
        SupportTicket::factory()->count(2)->create([
            "sender_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(SupportTicket::class, $this->record->supportTickets()->first());
        $this->assertCount(2, $this->record->supportTickets);
    }

    public function testItHasSupportTicketsRespondedTo()
    {
        SupportTicket::factory()->count(2)->create([
            "responder_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(SupportTicket::class, $this->record->supportTicketsRespondedTo()->first());
        $this->assertCount(2, $this->record->supportTicketsRespondedTo);
    }

    public function testItHasTournaments()
    {
        $this->record->tournaments()->attach(Tournament::factory()->count(2)->create());

        $this->assertInstanceOf(Tournament::class, $this->record->tournaments()->first());
        $this->assertCount(2, $this->record->tournaments);
    }

    /* Attributes */

    public function testItIsStreamer()
    {
        $user = User::factory()->create(["streamer" => true]);
        $user->type = UserType::STREAMER;
        $user->save();

        $this->assertTrue($user->is_streamer);
    }

    public function testItIsNotStreamer()
    {
        $user = User::factory()->create(["streamer" => false]);

        $this->assertFalse($user->is_streamer);
    }

    public function testItHasDefaultAvatar()
    {
        $this->assertEquals($this->defaultAvatar, $this->record->avatar);
    }

    /* Helpers */

    public function testItCreatesNotificationOnModel()
    {
        $tournament = Tournament::factory()->create();
        $result = $this->record->createNotificationWithModelAndDescription($tournament, "My description");
        $notification = Notification::first();

        $this->assertTrue($result);
        $this->assertEquals(Tournament::class, $notification->notifiable_type);
        $this->assertEquals($tournament->id, $notification->notifiable_id);
        $this->assertEquals("My description", $notification->description);
        $this->assertEquals($this->record->id, $notification->user_id);
    }

    public function testItIsLockedByTournamentId()
    {
        $tournaments = Tournament::factory()->count(2)->create();
        $tournaments->each(function ($tournament) {
            $tournament->users()->attach($this->record, [
                "locked" => true,
            ]);
        });

        $this->assertTrue($this->record->isLocked($tournaments[0]->id));
    }

    public function testItIsLockedByPivot()
    {
        $tournaments = Tournament::factory()->count(2)->create();
        $tournaments->each(function ($tournament) {
            $tournament->users()->attach($this->record, [
                "locked" => true,
            ]);
        });
        $user = $tournaments[0]->users()->first();

        $this->assertTrue($user->isLocked());
    }

    public function testItGetsEsportalUser()
    {
        $response = User::getEsportalUser("MrMotoX");

        $this->assertEquals("MrMotoX", $response["username"]);
    }

    public function testItGetsNoEsportalUserOnNotExisting()
    {
        $response = User::getEsportalUser("bad_name_does_not_exist");

        $this->assertNull($response);
    }

    public function testItGetsTypeString()
    {
        $this->assertEquals("SUPERADMIN", User::factory()->create([
            "type" => UserType::SUPERADMIN,
        ])->getType());
        $this->assertEquals("STREAMER", User::factory()->create([
            "type" => UserType::STREAMER,
        ])->getType());
        $this->assertEquals("GUEST", User::factory()->create([
            "type" => UserType::GUEST,
        ])->getType());
        $this->assertEquals("MEMBER", User::factory()->create([
            "type" => UserType::MEMBER,
        ])->getType());
        $this->assertEquals("BANNED", User::factory()->create([
            "type" => UserType::BANNED,
        ])->getType());
    }

    /* Events */

    // TODO Use the same kind of verification on other event tests
    public function testItCanDispatchTournamentOpenedForCheckInEvent()
    {
        Event::fake();

        $tournament = Tournament::factory()->create();
        $user = $this->record;

        $this->record->dispatchTournamentOpenedForCheckIn($tournament);

        Event::assertDispatched(function (TournamentOpenedForCheckInEvent $event) use ($tournament, $user) {
            if ($event->broadcastOn()->name !== "App.Models.User." . $user->id)
                return false;

            return $event->tournament->id === $tournament->id;
        });
    }

    public function testItCanDispatchPlayerDetachedFromTeamEvent()
    {
        Event::fake();

        $team = Team::factory()->create();
        $team->users()->attach($this->record);
        $user = $this->record;

        $this->record->dispatchPlayerDetachedFromTeam($team);

        Event::assertDispatched(function (PlayerDetachedFromTeamEvent $event) use ($team, $user) {
            if ($event->broadcastOn()->name !== "App.Models.User." . $user->id)
                return false;

            return $event->id === $team->id;
        });
    }

    public function testItCanDispatchTeamDestroyedEvent()
    {
        Event::fake();

        $team = Team::factory()->create();
        $team->users()->attach($this->record);
        $user = $this->record;

        $this->record->dispatchTeamDestroyed($team);

        Event::assertDispatched(function (TeamDestroyedEvent $event) use ($team, $user) {
            if ($event->broadcastOn()->name !== "App.Models.User." . $user->id)
                return false;

            return $event->id === $team->id;
        });
    }

    public function testItCanDispatchUserDeRegisteredFromTournamentEvent()
    {
        Event::fake();

        $user = $this->record;
        $tournament = Tournament::factory()->create();
        $tournament->users()->attach($user->id, [
            "state" => TournamentUserState::REGISTERED,
        ]);

        $this->record->dispatchUserDeRegisteredFromTournament($tournament);

        Event::assertDispatched(function (UserDeRegisteredFromTournamentEvent $event) use ($tournament, $user) {
            if ($event->broadcastOn()->name !== "App.Models.User." . $user->id)
                return false;

            return $event->user_id == $user->id;
        });
    }
}
