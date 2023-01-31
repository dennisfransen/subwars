<?php

namespace App\Models;

use App\Http\Traits\ReleaseStatusTrait;
use Database\Factories\BracketFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Bracket
 *
 * @property int $id
 * @property string $title
 * @property-read Collection|Tournament[] $tournaments
 * @property-read int|null $tournaments_count
 * @method static BracketFactory factory(...$parameters)
 * @method static Builder|Bracket newModelQuery()
 * @method static Builder|Bracket newQuery()
 * @method static Builder|Bracket query()
 * @method static Builder|Bracket whereId($value)
 * @method static Builder|Bracket whereTitle($value)
 * @mixin \Eloquent
 * @property int $status
 * @method static Builder|Bracket whereStatus($value)
 */
class Bracket extends Model
{
    use HasFactory, ReleaseStatusTrait;

    public $timestamps = false;

    /**
     * @return HasMany
     */
    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }
}
