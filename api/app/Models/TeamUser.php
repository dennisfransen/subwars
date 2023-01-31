<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * App\Models\TeamUser
 *
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Team $team
 * @property-read \App\Models\User $user
 * @method static Builder|TeamUser newModelQuery()
 * @method static Builder|TeamUser newQuery()
 * @method static Builder|TeamUser query()
 * @method static Builder|TeamUser whereCreatedAt($value)
 * @method static Builder|TeamUser whereId($value)
 * @method static Builder|TeamUser whereTeamId($value)
 * @method static Builder|TeamUser whereUpdatedAt($value)
 * @method static Builder|TeamUser whereUserId($value)
 * @mixin \Eloquent
 * @method static \Database\Factories\TeamUserFactory factory(...$parameters)
 */
class TeamUser extends Pivot
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
