<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LinkedSocialAccount
 *
 * @property int $id
 * @property string $provider_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $avatar
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\LinkedSocialAccountFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkedSocialAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LinkedSocialAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LinkedSocialAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|LinkedSocialAccount whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkedSocialAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkedSocialAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkedSocialAccount whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkedSocialAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkedSocialAccount whereUserId($value)
 * @mixin \Eloquent
 */
class LinkedSocialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        "provider_id",
        "user_id",
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
