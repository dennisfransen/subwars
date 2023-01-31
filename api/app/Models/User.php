<?php

namespace App\Models;

use App\Events\MultiChannelEvent;
use App\Events\PlayerDetachedFromTeamEvent;
use App\Events\TeamDestroyedEvent;
use App\Events\TournamentOpenedForCheckInEvent;
use App\Events\UserDeRegisteredFromTournamentEvent;
use App\Http\Enums\CasterRole;
use App\Http\Enums\UserType;
use Exception;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Client;
use Laravel\Passport\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int $type
 * @property string|null $username
 * @property string|null $password
 * @property int|null $esportal_elo
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $streamer
 * @property string|null $esportal_username
 * @property-read \Illuminate\Database\Eloquent\Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tournament[] $coCastedTournaments
 * @property-read int|null $co_casted_tournaments_count
 * @property-read string $avatar
 * @property-read bool $is_streamer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LinkedSocialAccount[] $linkedSocialAccounts
 * @property-read int|null $linked_social_accounts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Notification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tournament[] $ownedTournaments
 * @property-read int|null $owned_tournaments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sponsor[] $sponsors
 * @property-read int|null $sponsors_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SupportTicket[] $supportTickets
 * @property-read int|null $support_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SupportTicket[] $supportTicketsRespondedTo
 * @property-read int|null $support_tickets_responded_to_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tournament[] $tournaments
 * @property-read int|null $tournaments_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEsportalElo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEsportalUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStreamer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tournament[] $mainCastedTournaments
 * @property-read int|null $main_casted_tournaments_count
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwitchUrl($value)
 * @property string|null $twitch_state
 * @property string|null $twitch_scope
 * @property string|null $twitch_login
 * @property string|null $twitch_id
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwitchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwitchLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwitchScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwitchState($value)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "username",
        "password",
        "type",
        "esportal_elo",
        "esportal_username",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        "password",
        "remember_token",
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        "email_verified_at" => "datetime",
        "type" => "integer"
    ];

    /**
     * @return BelongsToMany
     */
    public function coCastedTournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class, "co_casters")
            ->wherePivot("role", CasterRole::CO_CASTER);
    }

    /**
     * @return BelongsToMany
     */
    public function mainCastedTournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class, "co_casters")
            ->wherePivot("role", CasterRole::MAIN_CASTER);
    }

    /**
     * @return HasMany
     */
    public function linkedSocialAccounts(): HasMany
    {
        return $this->hasMany(LinkedSocialAccount::class);
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
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, "sender_id");
    }

    /**
     * @return HasMany
     */
    public function supportTicketsRespondedTo(): HasMany
    {
        return $this->hasMany(SupportTicket::class, "responder_id");
    }

    /**
     * @return HasMany
     */
    public function ownedTournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, "user_id");
    }

    /**
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * @return BelongsToMany
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class)
            ->withPivot(["state"]);
    }

    /**
     * @param $username
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public function findForPassport($username)
    {
        return $this->where("username", $username)
            ->first();
    }

    /**
     * @param $client
     * @param array $request
     * @return array
     */
    public static function getCredentials($client, array $request): array
    {
        return [
            "grant_type" => "password",
            "client_id" => $client->id,
            "client_secret" => $client->secret,
            "username" => $request["username"],
            "password" => $request["password"],
        ];
    }

    /**
     * @return Model|Builder|object|null
     */
    public static function getClient()
    {
        return Client::where("password_client", true)->first();
    }

    /**
     * @param Model $model
     * @param string $description
     * @return bool
     */
    public function createNotificationWithModelAndDescription(Model $model, string $description): bool
    {
        $notification = new Notification();

        $notification->notifiable_type = get_class($model);
        $notification->notifiable_id = $model->id;
        $notification->description = $description;
        $notification->user_id = $this->id;

        return $notification->save();
    }

    /**
     * @return bool
     */
    public function getIsStreamerAttribute(): bool
    {
        return $this->type === UserType::STREAMER;
    }

    /**
     * @param string $suffix
     * @return string
     */
    public function getAvatarWithSuffix(string $suffix): string
    {
        $extensionPos = strrpos($this->avatar, ".");

        return substr($this->avatar, 0, $extensionPos) . $suffix . substr($this->avatar, $extensionPos);
    }

    /**
     * @return string
     */
    public function getAvatarAttribute(): string
    {
        if ($linkedSocialAccount = $this->linkedSocialAccounts()->first()) {
            if (strlen($linkedSocialAccount->avatar))
                return $linkedSocialAccount->avatar;
        }

        return "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg";
    }

    /**
     * @param int|null $tournamentId
     * @return bool
     */
    public function isLocked(int $tournamentId = null): bool
    {
        if ($this->pivot->locked ?? false)
            return true;

        if ($tournamentId)
            return Tournament::find($tournamentId)
                ->registered()
                ->where("id", $this->id)
                ->wherePivot("locked", true)
                ->count();

        return false;
    }

    /**
     * @param string|null $username
     * @return array|mixed|null
     */
    static public function getEsportalUser(string $username = null)
    {
        $res = Http::get('https://api.esportal.com/user_profile/get?username=' . $username);

        if ($res->status() !== 200)
            return null;

        return $res->json() ?? null;
    }

    /* Attributes */

    /**
     * @return string
     */
    public function getType(): string
    {
        return (new UserType())->getStringOfInteger($this->attributes["type"]);
    }

    /* Helpers */

    /**
     * @param User $streamer
     * @return bool
     * TODO Unit test
     * @throws Exception
     */
    public function isSubscriber(?User $streamer): bool
    {
        $userLookupResponse = Http::withHeaders(["Client-Id" => env("TWITCH_CLIENT_ID")])
            ->withToken(env("TWITCH_ACCESS_TOKEN"))
            ->get("https://api.twitch.tv/helix/users", ["id" => $streamer->twitch_id ?? null]);

        if ($userLookupResponse->status() != 200)
            throw new Exception("The streamer was not found on twitch.", 422);

        if (count($userLookupResponse->json("data")) <= 0)
            throw new Exception("The streamer was not found on twitch.", 422);

        $response = Http::withHeaders(["Client-Id" => env("TWITCH_CLIENT_ID")])
            ->withToken(env("TWITCH_ACCESS_TOKEN"))
            ->get("https://api.twitch.tv/helix/subscriptions/user?broadcaster_id=118368288&user_id=37264990", [
                "broadcaster_id" => $userLookupResponse->json("data.0.id"),
                "user_id" => $this->twitch_id,
            ]);

        if ($response->status() != 200)
            throw new Exception($response->json("message"), 422);

        return true;
    }

    /**
     * @param User $streamer
     * @return bool
     * @throws Exception
     * TODO Unit test
     */
    public function isFollower(?User $streamer): bool
    {
        $response = Http::withHeaders(["Client-Id" => env("TWITCH_CLIENT_ID")])
            ->withToken(env("TWITCH_ACCESS_TOKEN"))
            ->get("https://api.twitch.tv/helix/users/follows", [
                "from_id" => $this->twitch_id,
                "to_id" => $streamer->twitch_id ?? null,
            ]);

        if ($response->status() != 200)
            throw new Exception($response->json("message"), 422);

        if (!$response->json("total"))
            throw new Exception("The user is not a follower of the streamer on twitch.", 422);

        return true;
    }

    /* Events */

    /**
     * @param Tournament $tournament
     */
    public function dispatchTournamentOpenedForCheckIn(Tournament $tournament): void
    {
        try {
            event(new TournamentOpenedForCheckInEvent($tournament, MultiChannelEvent::CHANNEL_USER, $this->id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting tournament opened for check in event.");
        }
    }

    /**
     * @param Team $team
     */
    public function dispatchPlayerDetachedFromTeam(Team $team): void
    {
        try {
            event(new PlayerDetachedFromTeamEvent($team, $this->id, MultiChannelEvent::CHANNEL_USER, $this->id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting player detached from team event.");
        }
    }

    /**
     * @param Team $team
     */
    public function dispatchTeamDestroyed(Team $team): void
    {
        try {
            event(new TeamDestroyedEvent($team, MultiChannelEvent::CHANNEL_USER, $this->id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting team destroyed event.");
        }
    }

    /**
     * @param Tournament $tournament
     * @param int|null $teamId
     */
    public function dispatchUserDeRegisteredFromTournament(Tournament $tournament, int $teamId = null): void
    {
        try {
            event(new UserDeRegisteredFromTournamentEvent(
                $tournament,
                $this->id,
                $teamId,
                MultiChannelEvent::CHANNEL_USER,
                $this->id
            ));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting user de-registered event.");
        }
    }
}
