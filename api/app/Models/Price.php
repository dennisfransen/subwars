<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Price
 *
 * @property int $id
 * @property int $tournament_id
 * @property string $title
 * @property string $image_url
 * @property-read \App\Models\Tournament $tournament
 * @method static \Database\Factories\PriceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Price newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price query()
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereTournamentId($value)
 * @mixin \Eloquent
 */
class Price extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
