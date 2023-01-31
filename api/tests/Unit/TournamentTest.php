<?php

namespace Tests\Unit;

use App\Events\PlayerMovedEvent;
use App\Events\TeamsScrambledEvent;
use App\Events\TournamentUpdatedEvent;
use App\Events\UserDeRegisteredFromTournamentEvent;
use App\Http\Enums\TournamentUserState;
use App\Models\Bracket;
use App\Models\Fight;
use App\Models\Notification;
use App\Models\Price;
use App\Models\Sponsor;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;
use Event;
use Tests\TestCase;

class TournamentTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = Tournament::factory()->create();
    }

    /* Relationships */

    public function testItHasBracket()
    {
        $this->assertInstanceOf(Bracket::class, $this->record->bracket);
    }

    public function testItHasCheckedIn()
    {
        $this->record->checkedIn()->attach(User::factory()->count(2)->create(), [
            "state" => TournamentUserState::CHECKED_IN,
        ]);

        $this->assertInstanceOf(User::class, $this->record->checkedIn()->first());
        $this->assertCount(2, $this->record->checkedIn);
        $this->assertEquals(1, $this->record->checkedIn()->first()->pivot->order);
    }

    public function testItHasCoCasters()
    {
        $this->record->coCasters()->attach(User::factory()->count(2)->create()->pluck("id"));

        $this->assertInstanceOf(User::class, $this->record->coCasters()->first());
        $this->assertCount(2, $this->record->coCasters);
    }

    public function testItHasPrices()
    {
        Price::factory()->count(2)->create([
            "tournament_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(Price::class, $this->record->prices()->first());
        $this->assertCount(2, $this->record->prices);
    }

    public function testItHasCreator()
    {
        $this->assertInstanceOf(User::class, $this->record->creator);
    }

    public function testItHasFights() {
        Fight::factory()->count(2)->create([
            "tournament_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(Fight::class, $this->record->fights()->first());
        $this->assertCount(2, $this->record->fights);
    }

    public function testItHasNotifiable()
    {
        Notification::factory()->create([
            "notifiable_id" => $this->record->id,
            "notifiable_type" => Tournament::class,
        ]);

        $this->assertInstanceOf(Notification::class, $this->record->notifications()->first());
    }

    public function testItHasRegistered()
    {
        $this->record->registered()->attach(User::factory()->count(2)->create(), [
            "state" => TournamentUserState::REGISTERED,
        ]);

        $this->assertInstanceOf(User::class, $this->record->registered()->first());
        $this->assertCount(2, $this->record->registered);
        $this->assertEquals(1, $this->record->registered()->first()->pivot->order);
    }

    public function testItHasReserve()
    {
        $this->record->users()->attach(User::factory()->count(2)->create(), [
            "state" => TournamentUserState::CHECKED_IN,
        ]);

        $this->assertInstanceOf(User::class, $this->record->reserve()->first());
        $this->assertCount(2, $this->record->reserve);
        $this->assertEquals(1, $this->record->reserve()->first()->pivot->order);
    }

    public function testItHasSponsors()
    {
        $this->record->sponsors()->attach(Sponsor::factory()->count(2)->create());

        $this->assertInstanceOf(Sponsor::class, $this->record->sponsors()->first());
        $this->assertCount(2, $this->record->sponsors);
    }

    public function testItHasTeams() {
        Team::factory()->count(2)->create([
            "tournament_id" => $this->record->id,
        ]);

        $this->assertInstanceOf(Team::class, $this->record->teams()->first());
        $this->assertCount(2, $this->record->teams);
    }

    public function testItHasUsers()
    {
        $this->record->users()->attach(User::factory()->count(2)->create());

        $this->assertInstanceOf(User::class, $this->record->users()->first());
        $this->assertCount(2, $this->record->users);
        $this->assertEquals(1, $this->record->users()->first()->pivot->order);
    }

    /* Attributes */

    public function testItIsVisible()
    {
        $this->record->visible_at = Carbon::now()->subHour();
        $this->record->save();

        $this->assertTrue($this->record->is_visible);
    }

    public function testItIsNotVisible()
    {
        $this->record->visible_at = Carbon::now()->addHour();
        $this->record->save();

        $this->assertFalse($this->record->is_visible);
    }

    public function testItIsNotVisibleOnNull()
    {
        $this->record->visible_at = null;
        $this->record->save();

        $this->assertFalse($this->record->is_visible);
    }

    public function testItIsOpenForRegistration()
    {
        $this->record->registration_open_at = Carbon::now()->subHour();
        $this->record->save();

        $this->assertTrue($this->record->is_open_for_registration);
    }

    public function testItIsNotOpenForRegistration()
    {
        $this->record->registration_open_at = Carbon::now()->addHour();
        $this->record->save();

        $this->assertFalse($this->record->is_open_for_registration);
    }

    public function testItIsNotOpenForRegistrationOnNull()
    {
        $this->record->registration_open_at = null;
        $this->record->save();

        $this->assertFalse($this->record->is_open_for_registration);
    }

    public function testItIsOpenForCheckin()
    {
        $this->record->check_in_open_at = Carbon::now()->subHour();
        $this->record->save();

        $this->assertTrue($this->record->is_open_for_check_in);
    }

    public function testItIsNotOpenForCheckin()
    {
        $this->record->check_in_open_at = Carbon::now()->addHour();
        $this->record->save();

        $this->assertFalse($this->record->is_open_for_check_in);
    }

    public function testItIsNotOpenForCheckinOnNull()
    {
        $this->record->check_in_open_at = null;
        $this->record->save();

        $this->assertFalse($this->record->is_open_for_check_in);
    }

    public function testItHasLastOrder()
    {
        $this->record->users()->attach(User::factory()->create(), [
            "state" => TournamentUserState::REGISTERED,
            "order" => 1,
        ]);
        $this->record->users()->attach(User::factory()->create(), [
            "state" => TournamentUserState::REGISTERED,
            "order" => 2,
        ]);

        $this->assertEquals(2, $this->record->last_order);
    }

    public function testItHasZeroLastOrder()
    {
        $this->assertEquals(0, $this->record->last_order);
    }

    public function testItHasLastOrderWithCheckedIn()
    {
        $this->record->users()->attach(User::factory()->create(), [
            "state" => TournamentUserState::CHECKED_IN,
            "order" => 1,
        ]);

        $this->assertEquals(1, $this->record->last_order);
    }

    public function testItHasLastOrderWithRegisteredKicked()
    {
        $this->record->users()->attach(User::factory()->create(), [
            "state" => TournamentUserState::REGISTERED_KICKED,
            "order" => 1,
        ]);

        $this->assertEquals(1, $this->record->last_order);
    }

    public function testItHasLastOrderWithCheckedInKicked()
    {
        $this->record->users()->attach(User::factory()->create(), [
            "state" => TournamentUserState::CHECKED_IN_KICKED,
            "order" => 1,
        ]);

        $this->assertEquals(1, $this->record->last_order);
    }

    public function testItHasTeamPlayerCount()
    {
        $this->assertEquals(5, Tournament::TEAM_PLAYER_COUNT);
    }

    /* Events */

    // TODO Test event data
    public function testItDispatchesSingleTournamentUpdatedEvent()
    {
        Event::fake();

        $this->record->dispatchUpdatedSingleEvent();

        Event::assertDispatchedTimes(TournamentUpdatedEvent::class);
    }

    public function testItDispatchesPublicTournamentUpdatedEvent()
    {
        Event::fake();

        $this->record->dispatchUpdatedPublicEvent();

        Event::assertDispatchedTimes(TournamentUpdatedEvent::class);
    }

    public function testItDispatchesTeamsScrambledEvent()
    {
        Event::fake();

        $this->record->dispatchTeamsScrambledEvent();

        Event::assertDispatchedTimes(TeamsScrambledEvent::class);
    }

    public function testItDispatchesPlayerMovedEvent()
    {
        Event::fake();

        $this->record->dispatchPlayerMovedEvent();

        Event::assertDispatchedTimes(PlayerMovedEvent::class);
    }

    public function testItDispatchesUserDeRegisteredFromTournamentEvent()
    {
        Event::fake();

        $this->record->dispatchUserDeRegisteredFromTournamentEvent(1);

        Event::assertDispatchedTimes(UserDeRegisteredFromTournamentEvent::class);
    }

    public function testItDestroysNotificationsUponDeletion()
    {
        $user = User::factory()->create();
        $otherTournament = Tournament::factory()->create();
        $user->createNotificationWithModelAndDescription($this->record, "Description");
        $user->createNotificationWithModelAndDescription($this->record, "Description");
        $user->createNotificationWithModelAndDescription($otherTournament, "Description");

        $this->assertCount(3, $user->notifications()->get());

        $this->record->delete();

        $this->assertCount(1, $user->notifications()->get());
        $this->assertEquals($otherTournament->id, $user->notifications()->first()->notifiable_id);
    }

    /* Helpers */

    public function testItOpensRegistration()
    {
        $this->record->openRegistration();
        $this->record->refresh();

        $this->assertNotNull($this->record->registration_open_at);
        $this->assertTrue($this->record->registration_open_at <= Carbon::now());
    }

    public function testItKeepsOldDateOnOpenRegistration()
    {
        $expectedDate = Carbon::now()->subDays(2);
        $this->record->registration_open_at = $expectedDate;
        $this->record->save();

        $this->record->openRegistration();
        $this->record->refresh();

        $this->assertEquals($expectedDate, $this->record->registration_open_at);
    }

    public function testItReturnsFalseOnSecondOpenRegistration()
    {
        $this->record->openRegistration();

        $this->assertFalse($this->record->openRegistration());
    }

    public function testItOpensCheckIn()
    {
        $this->record->openCheckIn();
        $this->record->refresh();

        $this->assertNotNull($this->record->check_in_open_at);
        $this->assertTrue($this->record->check_in_open_at <= Carbon::now());
    }

    public function testItKeepsOldDateOnOpenCheckIn()
    {
        $expectedDate = Carbon::now()->subDays(2);
        $this->record->check_in_open_at = $expectedDate;
        $this->record->save();

        $this->record->openCheckIn();
        $this->record->refresh();

        $this->assertEquals($expectedDate, $this->record->check_in_open_at);
    }

    public function testItReturnsFalseOnSecondOpenCheckIn()
    {
        $this->record->openCheckIn();

        $this->assertFalse($this->record->openCheckIn());
    }

    public function testItPurgesEmptyTeams()
    {
        $teams = Team::factory()->count(4)->create([
            "tournament_id" => $this->record->id,
        ]);

        $teams[0]->users()->attach(User::factory()->count(5)->create()->pluck("id"));
        $teams[1]->users()->attach(User::factory()->count(2)->create()->pluck("id"));

        $this->record->purgeEmptyTeams();

        $this->assertCount(2, $this->record->teams);
    }
}
