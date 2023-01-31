<?php

namespace App\Models;

use App\Http\Enums\FightTeamResult;
use Database\Factories\FightFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Fight
 *
 * @property int $id
 * @property int $tournament_id
 * @property int|null $child_id
 * @property int $round
 * @property int $number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Fight|null $child
 * @property-read Collection|\App\Models\Team[] $losers
 * @property-read int|null $losers_count
 * @property-read Collection|Fight[] $parents
 * @property-read int|null $parents_count
 * @property-read Collection|\App\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read \App\Models\Tournament $tournament
 * @property-read Collection|\App\Models\Team[] $winners
 * @property-read int|null $winners_count
 * @method static \Database\Factories\FightFactory factory(...$parameters)
 * @method static Builder|Fight newModelQuery()
 * @method static Builder|Fight newQuery()
 * @method static Builder|Fight query()
 * @method static Builder|Fight whereChildId($value)
 * @method static Builder|Fight whereCreatedAt($value)
 * @method static Builder|Fight whereId($value)
 * @method static Builder|Fight whereNumber($value)
 * @method static Builder|Fight whereRound($value)
 * @method static Builder|Fight whereTournamentId($value)
 * @method static Builder|Fight whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Fight extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Fight::class, "child_id", "id");
    }

    /**
     * @return BelongsToMany
     */
    public function losers(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->wherePivot("result", FightTeamResult::LOSS);
    }

    /**
     * @return HasMany
     */
    public function parents(): HasMany
    {
        return $this->hasMany(Fight::class, "child_id", "id");
    }

    /**
     * @return BelongsToMany
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot(["result"]);
    }

    /**
     * @return BelongsTo
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * @return BelongsToMany
     */
    public function winners(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->wherePivot("result", FightTeamResult::WIN);
    }
}
