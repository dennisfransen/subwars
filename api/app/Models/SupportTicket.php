<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SupportTicket
 *
 * @property int $id
 * @property int|null $sender_id
 * @property string|null $email
 * @property int|null $responder_id
 * @property string|null $read_at
 * @property string $description
 * @property int $type
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $unread
 * @property-read \App\Models\User|null $responder
 * @property-read \App\Models\User|null $sender
 * @method static \Database\Factories\SupportTicketFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereResponderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SupportTicket extends Model
{
    use HasFactory;

    protected $casts = [
        "unread" => "boolean",
        "type" => "integer",
        "priority" => "integer",
    ];

    /**
     * @return BelongsTo
     */
    public function responder(): BelongsTo {
        return $this->belongsTo(User::class, "responder_id");
    }

    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo {
        return $this->belongsTo(User::class, "sender_id");
    }

    /**
     * @return bool
     */
    public function getUnreadAttribute(): bool {
        if ($this->read_at === null)
            return true;

        return $this->read_at <= Carbon::now();
    }
}
