<?php

namespace App\Models;

use App\Events\PlayerDetachedFromTeamEvent;
use App\Events\TeamDestroyedEvent;
use App\Events\TeamUpdatedEvent;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Team
 *
 * @property int $id
 * @property int $tournament_id
 * @property string $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\App\Models\Fight[] $fights
 * @property-read int|null $fights_count
 * @property-read int $average_elo
 * @property-read Collection|\App\Models\Notification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Tournament $tournament
 * @property-read Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\TeamFactory factory(...$parameters)
 * @method static Builder|Team newModelQuery()
 * @method static Builder|Team newQuery()
 * @method static Builder|Team query()
 * @method static Builder|Team whereCreatedAt($value)
 * @method static Builder|Team whereId($value)
 * @method static Builder|Team whereTitle($value)
 * @method static Builder|Team whereTournamentId($value)
 * @method static Builder|Team whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Team extends Model
{
    use HasFactory;

    protected $casts = [
        "tournament_id" => "integer",
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Team $team) {
            Notification::destroy($team->notifications()->pluck("id"));

            $team->users()->sync([]);
        });
    }

    /**
     * @return BelongsToMany
     */
    public function fights(): BelongsToMany
    {
        return $this->belongsToMany(Fight::class);
    }

    /**
     * @return BelongsTo
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, "notifiable");
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->orderByDesc("users.esportal_elo");
    }

    /**
     * @return int
     */
    public function getAverageEloAttribute(): int
    {
        return $this->users()->average("esportal_elo") ?? 0;
    }

    /**
     * @param int $targetElo
     * @return int
     */
    public function getOptimalEloToTarget(int $targetElo): int
    {
        $playerCount = $this->users()->count();

        if (!$playerCount)
            return $targetElo;

        if ($playerCount >= Tournament::TEAM_PLAYER_COUNT)
            return -1;

        $differenceEloPerPlayer = $targetElo - $this->averageElo;
        $requiredDifference = $differenceEloPerPlayer * ($playerCount + 1);

        return $this->averageElo + $requiredDifference;
    }

    /* Events */

    public function dispatchTeamUpdatedEvent(): void
    {
        try {
            event(new TeamUpdatedEvent($this));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting team updated event.");
        }
    }

    public function dispatchTeamDestroyedEvent(): void
    {
        try {
            event(new TeamDestroyedEvent($this));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting team destroyed event.");
        }
    }

    /**
     * @param int $userId
     * @param int $channel
     * @param int|null $channelId
     */
    public function dispatchPlayerDetachedFromTeamEvent(int $userId, int $channel, int $channelId = null): void
    {
        try {
            event(new PlayerDetachedFromTeamEvent($this, $userId, $channel, $channelId));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting player detached from team event.");
        }
    }

    /* Helpers */

    /**
     * @param User $user
     */
    public function attachPlayer(User $user): void
    {
        $this->users()->attach($user);
        $user->createNotificationWithModelAndDescription($this, "You were assigned a team.");
    }

    /**
     * @param User $user
     */
    public function detachPlayer(User $user): void
    {
        $this->users()->detach($user);
        $user->createNotificationWithModelAndDescription($this, "You were removed from a team.");
    }
}
