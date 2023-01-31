<?php

namespace App\Models;

use Database\Factories\SponsorFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Sponsor
 *
 * @property int $id
 * @property string $title
 * @property-read Collection|\App\Models\Tournament[] $tournaments
 * @property-read int|null $tournaments_count
 * @property-read Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\SponsorFactory factory(...$parameters)
 * @method static Builder|Sponsor newModelQuery()
 * @method static Builder|Sponsor newQuery()
 * @method static Builder|Sponsor query()
 * @method static Builder|Sponsor whereId($value)
 * @method static Builder|Sponsor whereTitle($value)
 * @mixin \Eloquent
 * @property string $image_url
 * @method static Builder|Sponsor whereImageUrl($value)
 */
class Sponsor extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * @return BelongsToMany
     */
    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }
}
