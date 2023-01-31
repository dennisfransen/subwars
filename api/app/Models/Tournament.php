<?php

namespace App\Models;

use App\Events\MultiChannelEvent;
use App\Events\PlayerMovedEvent;
use App\Events\PlayersSwitchedEvent;
use App\Events\TeamsScrambledEvent;
use App\Events\TournamentActionsUpdatedEvent;
use App\Events\TournamentStatsUpdatedEvent;
use App\Events\TournamentTeamsUpdatedEvent;
use App\Events\TournamentUpdatedEvent;
use App\Events\UserDeRegisteredFromTournamentEvent;
use App\Http\Enums\CasterRole;
use App\Http\Enums\CasterState;
use App\Http\Enums\TournamentEntryLevel;
use App\Http\Enums\TournamentUserState;
use Carbon\Carbon;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Tournament
 *
 * @property int $id
 * @property string $title
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $bracket_id
 * @property string|null $description
 * @property string|null $rules
 * @property int $max_teams
 * @property int $min_elo
 * @property int $max_elo
 * @property string|null $visible_at
 * @property string|null $live_at
 * @property string|null $registration_open_at
 * @property string|null $check_in_open_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Bracket|null $bracket
 * @property-read Collection|\App\Models\User[] $checkedIn
 * @property-read int|null $checked_in_count
 * @property-read Collection|\App\Models\User[] $coCasters
 * @property-read int|null $co_casters_count
 * @property-read \App\Models\User $creator
 * @property-read Collection|\App\Models\Fight[] $fights
 * @property-read int|null $fights_count
 * @property-read int $average_elo_reserve
 * @property-read int $even_player_count
 * @property-read bool $is_open_for_check_in
 * @property-read bool $is_open_for_registration
 * @property-read bool $is_visible
 * @property-read int $last_order
 * @property-read int $max_players
 * @property-read int $team_player_count
 * @property-read Collection|\App\Models\Notification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|\App\Models\User[] $registered
 * @property-read int|null $registered_count
 * @property-read Collection|\App\Models\User[] $reserve
 * @property-read int|null $reserve_count
 * @property-read Collection|\App\Models\Sponsor[] $sponsors
 * @property-read int|null $sponsors_count
 * @property-read Collection|\App\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\TournamentFactory factory(...$parameters)
 * @method static Builder|Tournament newModelQuery()
 * @method static Builder|Tournament newQuery()
 * @method static \Illuminate\Database\Query\Builder|Tournament onlyTrashed()
 * @method static Builder|Tournament query()
 * @method static Builder|Tournament whereBracketId($value)
 * @method static Builder|Tournament whereCheckInOpenAt($value)
 * @method static Builder|Tournament whereCreatedAt($value)
 * @method static Builder|Tournament whereDeletedAt($value)
 * @method static Builder|Tournament whereDescription($value)
 * @method static Builder|Tournament whereId($value)
 * @method static Builder|Tournament whereLiveAt($value)
 * @method static Builder|Tournament whereMaxElo($value)
 * @method static Builder|Tournament whereMaxTeams($value)
 * @method static Builder|Tournament whereMinElo($value)
 * @method static Builder|Tournament whereRegistrationOpenAt($value)
 * @method static Builder|Tournament whereRules($value)
 * @method static Builder|Tournament whereTitle($value)
 * @method static Builder|Tournament whereUpdatedAt($value)
 * @method static Builder|Tournament whereUserId($value)
 * @method static Builder|Tournament whereVisibleAt($value)
 * @method static \Illuminate\Database\Query\Builder|Tournament withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Tournament withoutTrashed()
 * @mixin \Eloquent
 * @property string $streamer_url
 * @method static Builder|Tournament whereStreamerUrl($value)
 * @property string $image
 * @property-read Collection|\App\Models\Price[] $prices
 * @property-read int|null $prices_count
 * @property-read Collection|\App\Models\User[] $registeredAndCheckedIn
 * @property-read int|null $registered_and_checked_in_count
 * @method static Builder|Tournament whereImage($value)
 * @property int $subscriber_only
 * @property int $follower_only
 * @method static Builder|Tournament whereFollowerOnly($value)
 * @method static Builder|Tournament whereSubscriberOnly($value)
 * @property string $entry_level
 * @property int $prioritize_by_entry_level
 * @method static Builder|Tournament whereEntryLevel($value)
 * @method static Builder|Tournament wherePrioritizeByEntryLevel($value)
 * @property-read int $average_elo
 * @property-read Collection|\App\Models\User[] $mainCasters
 * @property-read int|null $main_casters_count
 * @property int|null $game_id
 * @property int|null $platform_id
 * @method static Builder|Tournament whereGameId($value)
 * @method static Builder|Tournament wherePlatformId($value)
 */
class Tournament extends Model
{
    use HasFactory, SoftDeletes;

    const TEAM_PLAYER_COUNT = 5;

    protected $casts = [
        "user_id" => "int",
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Tournament $tournament) {
            Notification::destroy($tournament->notifications()->pluck("id"));
        });
    }

    /* Relationships */

    /**
     * @return BelongsTo
     */
    public function bracket(): BelongsTo
    {
        return $this->belongsTo(Bracket::class);
    }

    /**
     * @return BelongsToMany
     */
    public function checkedIn(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "tournament_user")
            ->withPivot(["state", "order"])
            ->wherePivot("state", TournamentUserState::CHECKED_IN);
    }

    /**
     * @return BelongsToMany
     */
    public function coCasters(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "co_casters")
            ->withPivot(["role"]);
    }

    /**
     * @return BelongsToMany
     * TODO Unit test
     */
    public function mainCasters(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "co_casters")
            ->where("tournament_id", $this->id)
            ->wherePivot("role", CasterRole::MAIN_CASTER)
            ->withPivot(["role"]);
    }

    /**
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    /**
     * @return HasMany
     */
    public function fights(): HasMany
    {
        return $this->hasMany(Fight::class);
    }

    /**
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, "notifiable");
    }

    /**
     * @return HasMany
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    /**
     * @return BelongsToMany
     */
    public function registered(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "tournament_user")
            ->withPivot(["state", "order", "locked", "entry_level"])
            ->wherePivot("state", ">=", TournamentUserState::REGISTERED);
    }

    /**
     * @return BelongsToMany
     */
    public function reserve(): BelongsToMany
    {
        $query = $this->belongsToMany(User::class, "tournament_user")
            ->withPivot(["state", "order", "locked", "entry_level"])
            ->where("tournament_id", $this->id)
            ->wherePivot("state", TournamentUserState::CHECKED_IN);

        $teamIds = $this->teams()->pluck("id");
        $playerIds = TeamUser::whereIn("team_id", $teamIds)->pluck("user_id");
        $query->whereNotIn("id", $playerIds);

        if ($this->maxPlayers > 0)
            $query->limit($this->maxPlayers - count($playerIds));

        return $query;
    }

    /**
     * @return BelongsToMany
     * TODO Unit test
     */
    public function registeredAndCheckedIn(): BelongsToMany
    {
        $query = $this->belongsToMany(User::class, "tournament_user")
            ->where("tournament_id", $this->id)
            ->wherePivot("state",">=", TournamentUserState::REGISTERED)
            ->withPivot(["state", "order", "locked", "entry_level"]);

        $teamIds = $this->teams()->pluck("id");
        $playerIds = TeamUser::whereIn("team_id", $teamIds)->pluck("user_id");
        $query->whereNotIn("id", $playerIds);

        return $query;
    }

    /**
     * @return BelongsToMany
     */
    public function sponsors(): BelongsToMany
    {
        return $this->belongsToMany(Sponsor::class);
    }

    /**
     * @return HasMany
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class)
            ->orderBy("title");
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(["state", "order", "locked"]);
    }

    /* Attributes */

    /**
     * @return bool
     */
    public function getIsVisibleAttribute(): bool
    {
        if ($this->visible_at == null)
            return false;

        return $this->visible_at <= Carbon::now();
    }

    /**
     * @return bool
     */
    public function getIsOpenForRegistrationAttribute(): bool
    {
        if ($this->registration_open_at == null)
            return false;

        return $this->registration_open_at <= Carbon::now();
    }

    /**
     * @return bool
     */
    public function getIsOpenForCheckInAttribute(): bool
    {
        if ($this->check_in_open_at == null)
            return false;

        return $this->check_in_open_at <= Carbon::now();
    }

    /**
     * @return int
     */
    public function getAverageEloReserveAttribute(): int
    {
        return $this->reserve()->average("esportal_elo") ?? 0;
    }

    /**
     * @return int
     * TODO Unit test
     */
    public function getAverageEloAttribute(): int
    {
        return $this->teams->average(function ($item) {
            return $item->averageElo;
        }) ?? 0;
    }

    /**
     * @return int
     */
    public function getMaxPlayersAttribute(): int
    {
        if ($this->max_teams < 0)
            return -1;

        return $this->max_teams * Tournament::TEAM_PLAYER_COUNT;
    }

    /**
     * @return int
     * TODO Unit test
     */
    public function getEvenPlayerCountAttribute(): int
    {
        $playerCount = $this->teams->sum(function ($team) {
                return $team->users()->count();
            }) + $this->reserve()->count();

        $evenPlayerCount = floor(($playerCount / Tournament::TEAM_PLAYER_COUNT)) * Tournament::TEAM_PLAYER_COUNT;

        return (int)($this->max_teams > 0) ? min($evenPlayerCount, $this->max_players) : $evenPlayerCount;
    }

    /**
     * @return int
     */
    public function getLastOrderAttribute(): int
    {
        $lastUser = $this->users()->orderByDesc("order")->first();
        return $lastUser->pivot->order ?? 0;
    }

    /**
     * @return string
     * TODO Unit test
     */
    public function getEntryLevelAttribute(): string
    {
        return (new TournamentEntryLevel())->getStringOfInteger($this->attributes["entry_level"]);
    }

    /**
     * @param $value
     * @return void
     * TODO Unit test
     */
    public function setEntryLevelAttribute($value)
    {
        $this->attributes["entry_level"] = (new TournamentEntryLevel())->getIntegerOfString($value);
    }

    /* Helpers */

    /**
     * @return bool
     */
    public function openRegistration(): bool
    {
        if (!$this->is_open_for_registration) {
            $this->registration_open_at = Carbon::now();
            return $this->save();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function openCheckIn(): bool
    {
        if (!$this->is_open_for_check_in) {
            $this->check_in_open_at = Carbon::now();
            return $this->save();
        }

        return false;
    }

    /**
     * @return int
     */
    public function scrambleTeamsGetAttachedPlayerCount(): int
    {
        $attachedPlayers = 0;

        $teamIds = $this->teams()->pluck("id");
        $lockedUserIds = $this->users()->where("locked", true)->pluck("id");
        $previousTeamUsers = TeamUser::whereIn("team_id", $teamIds)
            ->whereNotIn("user_id", $lockedUserIds);
        $previousUserIds = $previousTeamUsers->pluck("user_id");

        TeamUser::destroy($previousTeamUsers->pluck("id"));
        $this->teams()->doesntHave("users")->delete();

        $lockedTeamUserIds = TeamUser::whereIn("team_id", $teamIds)
            ->pluck("user_id");
        $teamUsers = $this->users()->whereIn("id", $lockedTeamUserIds)->get();

        $newTeamsCount = (int)($this->evenPlayerCount / Tournament::TEAM_PLAYER_COUNT) - $this->teams()->count();

        $teams = array();
        for ($i = 0; $i < $newTeamsCount; $i++) {
            $teams[] = $this->createTeam();
        }

        $teams = $this->teams()->get();

        $teamUsers = $teamUsers->merge($this->reserve()->whereIn("id", $previousUserIds)->get());
        $reserve = $teamUsers->merge($this->reserve()
            ->whereNotIn("id", $previousUserIds)
            ->get()
            ->take($this->teams()->count() * 5 - $teamUsers->count()));

        $attachedPlayers += $this->scramblePlayersToTeams(
            $teams,
            $reserve->sortBy("esportal_elo"),
            1
        );

        $attachedPlayers += $this->scramblePlayersToTeams(
            $teams,
            $reserve->sortBy("esportal_elo")->take(count($teams) - $reserve->count()),
            1
        );

        $attachedPlayers += $this->scramblePlayersToTeams(
            $teams,
            $reserve->sortByDesc("esportal_elo"),
            1
        );

        $attachedPlayers += $this->scramblePlayersToTeams(
            $teams,
            $reserve->sortByDesc("esportal_elo")->take(count($teams) - $reserve->count()),
            1
        );

        $teams = collect($teams)->sortBy(function ($team) {
            return $team->averageElo;
        })->values();

        $attachedPlayers += $this->scramblePlayersToTeams(
            $teams,
            $reserve->sortByDesc("esportal_elo")->take(count($teams) * 2 - $reserve->count()),
            1
        );

        return $attachedPlayers;
    }

    /**
     * @param \Illuminate\Support\Collection|array $teams
     * @param Collection $reserve
     * @param int $multiplier
     * @return int
     * TODO Test creation of notification
     */
    private function scramblePlayersToTeams($teams, Collection $reserve, int $multiplier): int
    {
        $attachedPlayers = 0;

        $locked = $reserve->take(count($teams) * $multiplier)->filter(function ($value) {
            return $value->pivot->locked;
        })->values();

        $notLocked = $reserve->take(count($teams) * $multiplier)->filter(function ($value) {
            return !$value->pivot->locked;
        })->values();

        $notLockedPointer = 0;
        for ($i = 0; $i < count($teams) * $multiplier; $i++) {
            if (!$teams[$i % count($teams)]->users()->whereIn("users.id", $locked->pluck("id"))->count()) {
                $teams[$i % count($teams)]->attachPlayer($notLocked[$notLockedPointer++]);
                $attachedPlayers++;
            }
        }

        return $attachedPlayers;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function getCasterStatus(int $userId): int
    {
        if ($this->coCasters()->where("users.id", $userId)->wherePivot("role", CasterRole::MAIN_CASTER)->count())
            return CasterState::MAIN_CASTER;

        if ($this->coCasters()->where("users.id", $userId)->wherePivot("role", CasterRole::CO_CASTER)->count())
            return CasterState::CO_CASTER;

        if ($this->user_id == $userId)
            return CasterState::OWNER;

        return 0;
    }

    /**
     * @param int $elo
     * @return User|null
     */
    public function getReservePlayerClosestToElo(int $elo): ?User
    {
        $upper = $this->reserve()
            ->where("esportal_elo", ">=", $elo)
            ->orderBy("esportal_elo")
            ->first();
        $lower = $this->reserve()
            ->where("esportal_elo", "<=", $elo)
            ->orderByDesc("esportal_elo")
            ->first();

        if ($upper && $lower) {
            $upperDelta = abs($elo - $upper->esportal_elo);
            $lowerDelta = abs($elo - $lower->esportal_elo);

            if ($upperDelta < $lowerDelta)
                return $upper;

            return $lower;
        } elseif ($upper) {
            return $upper;
        }

        return $lower;
    }

    public function purgeEmptyTeams(): void
    {
        $this->teams()->whereDoesntHave("users")->delete();
    }

    /* Events */

    public function dispatchUpdatedSingleEvent(): void
    {
        try {
            event(new TournamentUpdatedEvent($this, MultiChannelEvent::CHANNEL_TOURNAMENT, $this->id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting tournament updated event.");
        }
    }

    public function dispatchStatsUpdatedEvent(): void
    {
        try {
            event(new TournamentStatsUpdatedEvent($this, MultiChannelEvent::CHANNEL_TOURNAMENT, $this->id));
            event(new TournamentStatsUpdatedEvent($this, MultiChannelEvent::CHANNEL_TOURNAMENTS));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting tournament updated event.");
        }
    }

    public function dispatchActionsUpdatedEvent(): void
    {
        try {
            event(new TournamentActionsUpdatedEvent($this, MultiChannelEvent::CHANNEL_TOURNAMENT, $this->id));
            event(new TournamentActionsUpdatedEvent($this, MultiChannelEvent::CHANNEL_TOURNAMENTS));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting tournament updated event.");
        }
    }

    public function dispatchTeamsUpdatedEvent(): void
    {
        try {
            event(new TournamentTeamsUpdatedEvent($this, MultiChannelEvent::CHANNEL_TOURNAMENT, $this->id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting tournament updated event.");
        }
    }

    public function dispatchUpdatedPublicEvent(): void
    {
        try {
            event(new TournamentUpdatedEvent($this, MultiChannelEvent::CHANNEL_TOURNAMENTS));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting tournament updated event.");
        }
    }

    /**
     * @param array $userIds
     */
    public function dispatchPlayersSwitchedEvent(array $userIds): void
    {
        try {
            event(new PlayersSwitchedEvent($userIds, $this->id, MultiChannelEvent::CHANNEL_TOURNAMENT));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting players switched event.");
        }

        try {
            event(new PlayersSwitchedEvent($userIds, $userIds[0], MultiChannelEvent::CHANNEL_USER));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting players switched event.");
        }

        try {
            event(new PlayersSwitchedEvent($userIds, $userIds[1], MultiChannelEvent::CHANNEL_USER));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting players switched event.");
        }
    }

    public function dispatchTeamsScrambledEvent(): void
    {
        try {
            event(new TeamsScrambledEvent($this, $this->id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting teams scrambled event.");
        }
    }

    public function dispatchPlayerMovedEvent(): void
    {
        try {
            event(new PlayerMovedEvent($this, $this->id));
            event(new TournamentTeamsUpdatedEvent($this, MultiChannelEvent::CHANNEL_TOURNAMENT, $this->id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting player moved event.");
        }
    }

    /**
     * @param int $userId
     * @param int|null $teamId
     */
    public function dispatchUserDeRegisteredFromTournamentEvent(int $userId, int $teamId = null): void
    {
        try {
            event(new UserDeRegisteredFromTournamentEvent(
                $this,
                $userId,
                $teamId,
                MultiChannelEvent::CHANNEL_TOURNAMENT,
                $this->id
            ));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting user de-registered event.");
        }
    }

    /**
     * @return Team
     */
    public function createTeam(): Team
    {
        $team = new Team();

        for ($i = 0; $i < $this->teams()->count(); $i++) {
            if ($this->teams()->get()[$i]->title !== "Team " . ($i + 1)) {
                $team->title = "Team " . ($i + 1);
                $this->teams()->save($team);
                return $team;
            }
        }

        $team->title = "Team " . ($this->teams()->count() + 1);
        $this->teams()->save($team);
        return $team;
    }
}
