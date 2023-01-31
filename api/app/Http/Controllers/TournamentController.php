<?php

namespace App\Http\Controllers;

use App\Events\MultiChannelEvent;
use App\Events\PlayerLockedEvent;
use App\Events\PlayerUnlockedEvent;
use App\Events\TournamentActionsUpdatedEvent;
use App\Events\TournamentCreatedEvent;
use App\Events\TournamentDestroyedEvent;
use App\Events\TournamentGeneralUpdatedEvent;
use App\Events\TournamentOpenedForCheckInEvent;
use App\Events\TournamentOpenedForRegistrationEvent;
use App\Events\TournamentRulesUpdatedEvent;
use App\Events\TournamentStatsUpdatedEvent;
use App\Events\TournamentUpdatedEvent;
use App\Events\UserCheckedInToTournamentEvent;
use App\Events\UserRegisteredToTournamentEvent;
use App\Http\Enums\CasterRole;
use App\Http\Enums\TournamentEntryLevel;
use App\Http\Enums\TournamentUserState;
use App\Http\Requests\TournamentAddPlayerToTeamRequest;
use App\Http\Requests\TournamentAttachCoCasterRequest;
use App\Http\Requests\TournamentCheckInRequest;
use App\Http\Requests\TournamentDeRegisterRequest;
use App\Http\Requests\TournamentDetachCoCasterRequest;
use App\Http\Requests\TournamentDetachFromTeamsRequest;
use App\Http\Requests\TournamentIndexRequest;
use App\Http\Requests\TournamentKickPlayerRequest;
use App\Http\Requests\TournamentLockPlayerRequest;
use App\Http\Requests\TournamentRegisterRequest;
use App\Http\Requests\TournamentStoreRequest;
use App\Http\Requests\TournamentSwitchPlayersRequest;
use App\Http\Requests\TournamentUpdateRequest;
use App\Http\Resources\TeamResource;
use App\Http\Resources\TournamentActionsResource;
use App\Http\Resources\TournamentGeneralResource;
use App\Http\Resources\TournamentResource;
use App\Http\Resources\TournamentRulesResource;
use App\Http\Resources\TournamentStatsResource;
use App\Http\Resources\TournamentTeamsResource;
use App\Models\TeamUser;
use App\Models\Tournament;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Log;

class TournamentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param TournamentIndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(TournamentIndexRequest $request): AnonymousResourceCollection
    {
        $query = Tournament::with(["sponsors", "bracket", "coCasters"]);

        if ($request->has("min_live_at"))
            $query->where("live_at", ">=", $request->min_live_at);

        if ($request->has("max_live_at"))
            $query->where("live_at", "<=", $request->max_live_at);

        return TournamentResource::collection($query->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TournamentStoreRequest $request
     * @return TournamentResource
     * @throws AuthorizationException
     */
    public function store(TournamentStoreRequest $request): TournamentResource
    {
        $this->authorize("create", Tournament::class);

        $tournament = new Tournament();
        $tournament->title = $request->title;
        $tournament->bracket_id = 1;
        $tournament->user_id = auth()->user()->id;
        $tournament->description = $request->description;
        $tournament->streamer_url = $request->twitch_url ?? "";
        $tournament->rules = $request->rules;
        $tournament->max_teams = $request->max_teams;
        $tournament->min_elo = $request->min_elo ?? -1;
        $tournament->max_elo = $request->max_elo ?? -1;
        $tournament->visible_at = $request->visible_at;
        $tournament->registration_open_at = $request->registration_open_at;
        $tournament->live_at = $request->live_at;
        $tournament->check_in_open_at = $request->check_in_open_at;
        $tournament->entry_level = $request->entry_level ?? (new TournamentEntryLevel())->getStringOfInteger(TournamentEntryLevel::NONE);
        $tournament->prioritize_by_entry_level = $request->prioritize_by_entry_level ?? false;
        $tournament->save();

        try {
            event(new TournamentCreatedEvent($tournament, TournamentCreatedEvent::CHANNEL_TOURNAMENTS, $tournament->id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting tournament created event.");
        }

        try {
            event(new TournamentCreatedEvent($tournament, TournamentCreatedEvent::CHANNEL_USER, $tournament->user_id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting tournament created event.");
        }

        return new TournamentResource($tournament);
    }

    /**
     * Display the specified resource.
     *
     * @param Tournament $tournament
     * @return TournamentResource
     */
    public function show(Tournament $tournament): TournamentResource
    {
        return new TournamentResource($tournament->load(["sponsors", "teams.users", "reserve", "coCasters", "prices"]));
    }

    /**
     * @param Tournament $tournament
     * @return TournamentStatsResource
     */
    public function show_stats(Tournament $tournament): TournamentStatsResource
    {
        return new TournamentStatsResource($tournament);
    }

    /**
     * @param Tournament $tournament
     * @return TournamentGeneralResource
     */
    public function show_general(Tournament $tournament): TournamentGeneralResource
    {
        return new TournamentGeneralResource(Tournament::whereId($tournament->id)
            ->with(["sponsors", "creator"])
            ->first());
    }

    /**
     * @param Tournament $tournament
     * @return TournamentRulesResource
     */
    public function show_rules(Tournament $tournament): TournamentRulesResource
    {
        return new TournamentRulesResource($tournament);
    }

    /**
     * @param Tournament $tournament
     * @return TournamentTeamsResource
     */
    public function show_teams(Tournament $tournament): TournamentTeamsResource
    {
        return new TournamentTeamsResource(Tournament::whereId($tournament->id)
            ->with(["registered", "reserve", "teams"])
            ->first());
    }

    /**
     * @param Tournament $tournament
     * @return TournamentActionsResource
     */
    public function show_actions(Tournament $tournament): TournamentActionsResource
    {
        return new TournamentActionsResource($tournament);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TournamentUpdateRequest $request
     * @param Tournament $tournament
     * @return TournamentResource
     * @throws AuthorizationException
     * TODO Extend with additional attributes
     */
    public function update(TournamentUpdateRequest $request, Tournament $tournament): TournamentResource
    {
        $this->authorize("update", $tournament);

        $statsUpdated = (
            $request->max_teams != null and $request->max_teams != $tournament->max_teams
            or $request->min_elo != null and $request->min_elo != $tournament->min_elo
            or $request->max_elo != null and $request->max_elo != $tournament->max_elo
        );
        $tournament->max_teams = $request->max_teams ?? $tournament->max_teams;
        $tournament->min_elo = $request->min_elo ?? $tournament->min_elo;
        $tournament->max_elo = $request->max_elo ?? $tournament->max_elo;

        $generalUpdated = (
            $request->title != null and $request->title != $tournament->title
            or $request->description != null and $request->description != $tournament->description
            or $request->twitch_url != null and $request->twitch_url != $tournament->streamer_url
        );
        $tournament->title = $request->title ?? $tournament->title;
        $tournament->description = $request->description ?? $tournament->description;
        $tournament->streamer_url = $request->twitch_url ?? $tournament->streamer_url;

        $rulesUpdated = ($request->rules != null and $request->rules != $tournament->rules);
        $tournament->rules = $request->rules ?? $tournament->rules;

        $datesUpdated = (
            $request->visible_at != null and $request->visible_at != $tournament->visible_at
            or $request->live_at != null and $request->live_at != $tournament->live_at
            or $request->registration_open_at != null and $request->registration_open_at != $tournament->registration_open_at
            or $request->check_in_open_at != null and $request->check_in_open_at != $tournament->check_in_open_at
            or $request->entry_level != null and $request->entry_level != $tournament->entry_level
        );
        $tournament->visible_at = $request->visible_at ?? $tournament->visible_at;
        $tournament->live_at = $request->live_at ?? $tournament->live_at;
        $tournament->registration_open_at = $request->registration_open_at ?? $tournament->registration_open_at;
        $tournament->check_in_open_at = $request->check_in_open_at ?? $tournament->check_in_open_at;
        $tournament->entry_level = $request->entry_level ?? $tournament->entry_level;

        $tournament->bracket_id = 1;
        $tournament->prioritize_by_entry_level = $request->prioritize_by_entry_level ?? $tournament->prioritize_by_entry_level;

        if ($tournament->isDirty()) {
            $tournament->save();
            $tournament->load(["registered"]);

            try {
                if ($generalUpdated) {
                    event(new TournamentGeneralUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENT, $tournament->id));
                    event(new TournamentGeneralUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENTS));
                }

                if ($statsUpdated) {
                    event(new TournamentStatsUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENT, $tournament->id));
                }

                if ($rulesUpdated) {
                    event(new TournamentRulesUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENT, $tournament->id));
                }

                if ($datesUpdated) {
                    event(new TournamentActionsUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENT, $tournament->id));
                    event(new TournamentActionsUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENTS));
                }

                event(new TournamentUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENT, $tournament->id));
                event(new TournamentUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENTS));
            } catch (BroadcastException $e) {
                Log::error("Failed broadcasting events.");
            }
        }

        return new TournamentResource($tournament->load(["bracket", "creator", "sponsors", "teams.users", "reserve", "coCasters", "prices"]));
    }

    /**
     * @param Request $request
     * @param Tournament $tournament
     * @return TournamentResource
     */
    public function attachment(Request $request, Tournament $tournament): TournamentResource
    {
        if ($request->hasFile("image")) {
            $file = $request->file("image");
            $name = date("YmdHi") . $file->getClientOriginalName();
            $request->file("image")->storeAs("public", $name);
            $tournament->image = $name;
        }

        if ($tournament->isDirty()) {
            $tournament->save();
            $tournament->load(["registered"]);

            try {
                event(new TournamentUpdatedEvent($tournament, TournamentUpdatedEvent::CHANNEL_TOURNAMENT, $tournament->id));
            } catch (BroadcastException $e) {
                Log::error("Failed broadcasting payment event.");
            }

            try {
                event(new TournamentUpdatedEvent($tournament, TournamentUpdatedEvent::CHANNEL_TOURNAMENTS));
            } catch (BroadcastException $e) {
                Log::error("Failed broadcasting payment event.");
            }
        }

        return new TournamentResource($tournament);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tournament $tournament
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(Tournament $tournament): Response
    {
        $this->authorize("delete", $tournament);

        if ($deleted = $tournament->delete()) {
            try {
                event(new TournamentDestroyedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENTS));
            } catch (BroadcastException $e) {
                Log::error("Failed broadcasting tournament destroyed event.");
            }

            try {
                event(new TournamentDestroyedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENT, $tournament->id));
            } catch (BroadcastException $e) {
                Log::error("Failed broadcasting tournament destroyed event.");
            }

            try {
                event(new TournamentDestroyedEvent($tournament, MultiChannelEvent::CHANNEL_USER, $tournament->user_id));
            } catch (BroadcastException $e) {
                Log::error("Failed broadcasting tournament destroyed event.");
            }
        }

        return new Response($deleted);
    }

    /**
     * @param TournamentRegisterRequest $request
     * @param Tournament $tournament
     * @return Response
     * TODO Test the creation of the notification
     */
    public function register(TournamentRegisterRequest $request, Tournament $tournament): Response
    {
        if ($request->has("esportal_elo")) {
            User::find($request->user_id)->update(["esportal_elo" => $request->esportal_elo]);

            if ($request->esportal_elo < $tournament->min_elo && $tournament->min_elo > 0)
                return new Response([
                    "message" => "The esportal elo of the user is too low.",
                ], 422);

            if ($request->esportal_elo > $tournament->max_elo && $tournament->max_elo > 0)
                return new Response([
                    "message" => "The esportal elo of the user is too high.",
                ], 422);
        }

        if (!$tournament->is_open_for_registration)
            return new Response([
                "message" => "Tournament is not open for registration.",
            ], 422);

        try {
            $entryLevel = $this->verifyEntryLevel($tournament, User::whereId($request->user_id)->first());
        } catch (Exception $e) {
            return new Response([
                "message" => $e->getMessage(),
            ], $e->getCode());
        }

        if ($tournament->entry_level == (new TournamentEntryLevel())->getStringOfInteger(TournamentEntryLevel::MINIMUM_FOLLOWER)) {
            try {
                User::whereId($request->user_id)->first()->isFollower($tournament->mainCasters()->first());
            } catch (Exception $e) {
                return new Response([
                    "message" => $e->getMessage(),
                ], $e->getCode());
            }
        }

        $tournament->users()->attach($request->user_id, [
            "state" => TournamentUserState::REGISTERED,
            "order" => $tournament->last_order + 1,
            "entry_level" => $entryLevel,
        ]);
        User::find($request->user_id)->createNotificationWithModelAndDescription($tournament, "You were registered to a tournament.");
        $tournament->load(["registered"]);

        try {
            event(new UserRegisteredToTournamentEvent($tournament, User::find($request->user_id)));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting user registered event.");
        }

        $tournament->dispatchUpdatedSingleEvent();
        $tournament->dispatchStatsUpdatedEvent();

        return new Response();
    }

    /**
     * @param TournamentAddPlayerToTeamRequest $request
     * @param Tournament $tournament
     * @return TeamResource
     * TODO Test
     */
    public function add_player_to_team(TournamentAddPlayerToTeamRequest $request, Tournament $tournament): TeamResource
    {
        $user = User::find($request->user_id);

        $teamIds = $tournament->teams()->pluck("id");
        TeamUser::whereIn("team_id", $teamIds)->whereUserId($user->id)->delete();

        if ($request->has("team_id")) {
            $team = $tournament->teams()->where("id", $request->team_id)->first();
            $team->users()->attach($user->id);
        } else {
            $availableTeams = $tournament->teams()->with(["users"])->get()->filter(function ($item) {
                return count($item->users) < 5;
            });

            if (count($availableTeams)) {
                $averageElo = $tournament->averageElo;

                $team = $availableTeams->sort(function ($team) use ($averageElo, $user) {
                    $eloSum = $team->users->sum(function ($teamUser) {
                        return $teamUser->esportal_elo;
                    });
                    $eloSum += $user->esportal_elo;
                    $newAverageElo = $eloSum / count($team->users);

                    return abs($averageElo - $newAverageElo);
                })->last();
            } else {
                $team = $tournament->createTeam();
            }

            $team->users()->attach($user->id);
        }

        $tournament->purgeEmptyTeams();

        $tournament->dispatchPlayerMovedEvent();

        return new TeamResource($team->load(["users"]));
    }

    /**
     * @param TournamentDeRegisterRequest $request
     * @param Tournament $tournament
     * @return Response
     * TODO Test that user gets detached from team, if there is any
     */
    public function de_register(TournamentDeRegisterRequest $request, Tournament $tournament): Response
    {
        $tournament->users()->detach($request->user_id);
        $user = User::find($request->user_id);
        $user->createNotificationWithModelAndDescription($tournament, "You were de-registered from a tournament.");

        $teamIds = $user->teams()->where("tournament_id", $tournament->id)->pluck("teams.id");
        $user->teams()->detach($teamIds);

        $tournament->dispatchUserDeRegisteredFromTournamentEvent($request->user_id, $teamIds[0] ?? null);
        $tournament->dispatchStatsUpdatedEvent();
        $user->dispatchUserDeRegisteredFromTournament($tournament, $teamIds[0] ?? null);

        return new Response();
    }

    /**
     * @param TournamentCheckInRequest $request
     * @param Tournament $tournament
     * @return Response
     */
    public function check_in(TournamentCheckInRequest $request, Tournament $tournament): Response
    {
        $user = User::find($request->user_id);
        if ($request->has("esportal_elo")) {
            $user->update(["esportal_elo" => $request->esportal_elo]);

            if ($request->esportal_elo < $tournament->min_elo && $tournament->min_elo > 0)
                return new Response([
                    "message" => "The esportal elo of the user is too low.",
                ], 422);

            if ($request->esportal_elo > $tournament->max_elo && $tournament->max_elo > 0)
                return new Response([
                    "message" => "The esportal elo of the user is too high.",
                ], 422);
        }

        if (!$tournament->is_open_for_check_in)
            return new Response([
                "message" => "Tournament is not open for check in.",
            ], 422);

        try {
            $entryLevel = $this->verifyEntryLevel($tournament, User::whereId($request->user_id)->first());
        } catch (Exception $e) {
            return new Response([
                "message" => $e->getMessage(),
            ], $e->getCode());
        }

        $tournament->users()->updateExistingPivot($request->user_id, [
            "state" => TournamentUserState::CHECKED_IN,
            "entry_level" => $entryLevel,
        ]);
        $user->createNotificationWithModelAndDescription($tournament, "You were checked in to a tournament.");
        $tournament->load(["registered"]);

        try {
            event(new UserCheckedInToTournamentEvent($tournament, User::find($request->user_id)));
            event(new TournamentStatsUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENT, $tournament->id));
            event(new TournamentStatsUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENTS));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting user checked in event.");
        }

        $tournament->dispatchUpdatedSingleEvent();
        $tournament->dispatchStatsUpdatedEvent();

        return new Response();
    }

    /**
     * @param TournamentKickPlayerRequest $request
     * @param Tournament $tournament
     * @return Response
     * @throws AuthorizationException
     */
    public function kick_player(TournamentKickPlayerRequest $request, Tournament $tournament): Response
    {
        $this->authorize("update", $tournament);

        $user = User::find($request->user_id);
        $priorState = $tournament->users()->where("user_id", $request->user_id)->first()->pivot->state;

        $tournament->users()->updateExistingPivot($request->user_id, [
            "state" => abs($priorState) * -1,
        ]);
        $teamIds = $tournament->teams()->pluck("id");
        TeamUser::whereIn("team_id", $teamIds)->where("user_id", $request->user_id)->delete();
        $user->createNotificationWithModelAndDescription($tournament, "You were kicked from a tournament.");
        $tournament->load(["registered"]);

        $tournament->dispatchUpdatedSingleEvent();
        $tournament->dispatchStatsUpdatedEvent();

        return new Response();
    }

    /**
     * @param Tournament $tournament
     * @return Response
     * @throws AuthorizationException
     */
    public function open_registration(Tournament $tournament): Response
    {
        $this->authorize("update", $tournament);

        if ($tournament->is_open_for_registration)
            return new Response([
                "message" => "Tournament is already open for registration.",
            ], 422);

        if ($saved = $tournament->openRegistration()) {
            $tournament->dispatchActionsUpdatedEvent();

            try {
                event(new TournamentOpenedForRegistrationEvent($tournament));
                event(new TournamentUpdatedEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENTS));
            } catch (BroadcastException $e) {
                Log::error("Failed broadcasting tournament opened for registration event.");
            }
        }

        return new Response($saved);
    }

    /**
     * @param Tournament $tournament
     * @return Response
     * @throws AuthorizationException
     */
    public function open_check_in(Tournament $tournament): Response
    {
        $this->authorize("update", $tournament);

        if ($tournament->is_open_for_check_in)
            return new Response([
                "message" => "Tournament is already open for check in.",
            ], 422);

        if ($saved = $tournament->openCheckIn()) {
            $tournament->dispatchActionsUpdatedEvent();

            try {
                event(new TournamentOpenedForCheckInEvent($tournament, MultiChannelEvent::CHANNEL_TOURNAMENT, $tournament->id));
                event(new TournamentUpdatedEvent($tournament->load(["registered"]), MultiChannelEvent::CHANNEL_TOURNAMENTS));
            } catch (BroadcastException $e) {
                Log::error("Failed broadcasting tournament opened for check in event.");
            }

            foreach ($tournament->registered as $user) {
                $user->dispatchTournamentOpenedForCheckIn($tournament);
                $user->createNotificationWithModelAndDescription($tournament, "Tournament opened for check in.");
            }
        }

        return new Response($saved);
    }

    /**
     * @param Tournament $tournament
     * @return TournamentResource|Response
     * @throws AuthorizationException
     */
    public function scramble_teams(Tournament $tournament)
    {
        $this->authorize("update", $tournament);

        if ($tournament->max_players > 0) {
            $teamIds = $tournament->teams()->pluck("id");

            if (TeamUser::whereIn("team_id", $teamIds)->count() > $tournament->max_players)
                return new Response([
                    "message" => "There are too many players in the teams to perform a valid scramble. Lower the amount of players in the teams and try again.",
                    "errors" => [
                        "teams" => [
                            "There are too many players in the teams.",
                        ],
                    ],
                ], 422);
        }

        $attachedPlayers = $tournament->scrambleTeamsGetAttachedPlayerCount();

        $tournament->dispatchTeamsUpdatedEvent();
        $tournament->load(["teams.users", "reserve"]);

        if ($attachedPlayers) {
            $tournament->dispatchTeamsScrambledEvent();
        }

        return new TournamentResource($tournament);
    }

    /**
     * @param TournamentAttachCoCasterRequest $request
     * @param Tournament $tournament
     * @return Response
     * @throws AuthorizationException
     */
    public function attach_co_caster(TournamentAttachCoCasterRequest $request, Tournament $tournament): Response
    {
        $this->authorize("update", $tournament);

        $tournament->coCasters()->detach($request->user_id);

        $tournament->coCasters()->attach($request->user_id, [
            "role" => (new CasterRole())->getIntegerOfString($request->role),
        ]);

        return new Response();
    }

    /**
     * @param TournamentDetachCoCasterRequest $request
     * @param Tournament $tournament
     * @return Response
     * @throws AuthorizationException
     */
    public function detach_co_caster(TournamentDetachCoCasterRequest $request, Tournament $tournament): Response
    {
        $this->authorize("update", $tournament);

        $tournament->coCasters()->detach($request->user_id);

        return new Response();
    }

    /**
     * @param TournamentSwitchPlayersRequest $request
     * @param Tournament $tournament
     * @return Response
     * @throws AuthorizationException
     * TODO Test the creation of notifications
     */
    public function switch_players(TournamentSwitchPlayersRequest $request, Tournament $tournament): Response
    {
        $this->authorize("update", $tournament);

        $teamUsers = TeamUser::whereIn("user_id", $request->user_ids)
            ->whereIn("team_id", $tournament->teams()->pluck("id"))
            ->get();

        if ($teamUsers->count() < 2) {
            if ($teamUsers[0]->user_id == $request->user_ids[0]) {
                $teamUsers[0]->update(["user_id" => $request->user_ids[1]]);
            } else {
                $teamUsers[0]->update(["user_id" => $request->user_ids[0]]);
            }
            $teamUsers[0]->user->createNotificationWithModelAndDescription($teamUsers[0]->team, "You were assigned to a team.");
        } else {
            $firstTeamId = $teamUsers[0]->team_id;
            $secondTeamId = $teamUsers[1]->team_id;

            $teamUsers[0]->update(["team_id" => $secondTeamId]);
            $teamUsers[1]->update(["team_id" => $firstTeamId]);

            $teamUsers[0]->user->createNotificationWithModelAndDescription($teamUsers[0]->team, "You were assigned to a team.");
            $teamUsers[1]->user->createNotificationWithModelAndDescription($teamUsers[1]->team, "You were assigned to a team.");
        }

        $tournament->dispatchPlayersSwitchedEvent($request->user_ids);
        $tournament->dispatchTeamsUpdatedEvent();

        return new Response();
    }

    /**
     * @param TournamentDetachFromTeamsRequest $request
     * @param Tournament $tournament
     * @return Response
     * @throws AuthorizationException
     */
    public function detach_from_teams(TournamentDetachFromTeamsRequest $request, Tournament $tournament): Response
    {
        $this->authorize("update", $tournament);

        $query = TeamUser::whereIn("team_id", $tournament->teams()->pluck("id"));

        if ($request->user_ids) {
            $query->whereNotIn("user_id", $request->user_ids);
        }

        $query->whereNotIn("user_id", $tournament->users()->whereLocked(true)->pluck("id"));

        $detachedPlayerCount = $query->count();

        $query->delete();

        $tournament->purgeEmptyTeams();

        $tournament->load(["registered", "teams.users", "reserve"]);
        if ($detachedPlayerCount) {
            $tournament->dispatchUpdatedSingleEvent();
            $tournament->dispatchTeamsUpdatedEvent();
            $tournament->dispatchUpdatedPublicEvent();
        }

        return new Response();
    }

    /**
     * @param TournamentLockPlayerRequest $request
     * @param Tournament $tournament
     * @return Response
     * TODO Add events
     * TODO Check that user is within registered users
     */
    public function lock_player(TournamentLockPlayerRequest $request, Tournament $tournament): Response
    {
        $tournament->users()->updateExistingPivot($request->user_id, ["locked" => true]);

        try {
            event(new PlayerLockedEvent($tournament, $request->user_id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting user locked event.");
        }

        return new Response();
    }

    /**
     * @param TournamentLockPlayerRequest $request
     * @param Tournament $tournament
     * @return Response
     * TODO Add events
     * TODO Check that user is within registered users
     */
    public function unlock_player(TournamentLockPlayerRequest $request, Tournament $tournament): Response
    {
        $tournament->users()->updateExistingPivot($request->user_id, ["locked" => false]);

        try {
            event(new PlayerUnlockedEvent($tournament, $request->user_id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting user locked event.");
        }

        return new Response();
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     * @return int
     * @throws Exception
     */
    private function verifyEntryLevel(Tournament $tournament, User $user): int
    {
        $entryLevel = (new TournamentEntryLevel())->getIntegerOfString($tournament->entry_level);
        $mainCaster = $tournament->mainCasters()->first();

        try {
            if ($user->isSubscriber($mainCaster))
                return TournamentEntryLevel::MINIMUM_SUBSCRIBER;
        } catch (Exception $e) {
            if ($entryLevel >= TournamentEntryLevel::MINIMUM_SUBSCRIBER)
                throw $e;
        }

        try {
            if ($user->isFollower($mainCaster))
                return TournamentEntryLevel::MINIMUM_FOLLOWER;
        } catch (Exception $e) {
            if ($entryLevel >= TournamentEntryLevel::MINIMUM_FOLLOWER)
                throw $e;
        }

        return TournamentEntryLevel::NONE;
    }
}
