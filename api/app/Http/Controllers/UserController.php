<?php

namespace App\Http\Controllers;

use App\Http\Enums\CasterState;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Http\Requests\UserAttachSponsorRequest;
use App\Http\Requests\UserDetachSponsorRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\TournamentIdsResource;
use App\Http\Resources\TournamentStateResource;
use App\Http\Resources\UserCompleteResource;
use App\Http\Resources\UserSimpleResource;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $usersQuery = User::query();

        if ($request->has("esportal_username"))
            $usersQuery = $usersQuery->where("esportal_username", "LIKE", "%" . $request->esportal_username . "%");

        if ($request->streamers)
            $usersQuery = $usersQuery->where("streamer", true);

        // TODO Add live filter that only shows streamers that are live

        return UserSimpleResource::collection($usersQuery->get());
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return UserCompleteResource
     */
    public function show(User $user): UserCompleteResource
    {
        return new UserCompleteResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return UserCompleteResource
     * TODO Test different fields
     * @throws AuthorizationException
     */
    public function update(UserUpdateRequest $request, User $user): UserCompleteResource
    {
        if ($request->has("esportal_username")) {
            $user->esportal_username = $request->esportal_username;
            $user->esportal_elo = $request->esportal_elo;
        }

        if ($request->has("type")) {
            $this->authorize("update", $user);

            $user->type = (new UserType())->getIntegerOfString($request->type);
        }

        if ($user->isDirty())
            $user->save();

        return new UserCompleteResource($user);
    }

    /**
     * @param UserAttachSponsorRequest $request
     * @param User $user
     * @return Response
     * @throws AuthorizationException
     */
    public function attach_sponsor(UserAttachSponsorRequest $request, User $user): Response
    {
        $this->authorize("attachSponsor", $user);

        $user->sponsors()->attach($request->sponsor_id);

        return new Response();
    }

    /**
     * @param UserDetachSponsorRequest $request
     * @param User $user
     * @return Response
     * @throws AuthorizationException
     */
    public function detach_sponsor(UserDetachSponsorRequest $request, User $user): Response
    {
        $this->authorize("attachSponsor", $user);

        $user->sponsors()->detach($request->sponsor_id);

        return new Response();
    }

    /**
     * @return AnonymousResourceCollection
     * TODO Test
     */
    public function tournament_ids(): AnonymousResourceCollection
    {
        $tournaments = auth()
            ->user()
            ->tournaments()
            ->wherePivot("state", ">=", TournamentUserState::REGISTERED)
            ->get();

        return TournamentIdsResource::collection($tournaments);
    }

    /**
     * @param User $user
     * @param Tournament $tournament
     * @return JsonResource
     * TODO Feature test
     */
    public function status_by_tournament(User $user, Tournament $tournament): JsonResource
    {
        $state = $user->tournaments()->where("id", $tournament->id)->first()->pivot->state ?? 0;

        return new JsonResource([
            "state" => (new TournamentUserState())->getStringOfInteger($state),
            "caster" => (new CasterState())->getStringOfInteger($tournament->getCasterStatus($user->id)),
        ]);
    }

    /**
     * @param User $user
     * @return AnonymousResourceCollection
     * TODO Make this more efficient
     * TODO Feature test
     */
    public function status_all_tournaments(User $user): AnonymousResourceCollection
    {
        $statedTournaments = $user->tournaments;
        $coCastedTournaments = $user->coCastedTournaments;
        $mainCastedTournaments = $user->mainCastedTournaments;
        $ownedTournaments = $user->ownedTournaments;
        $tournaments = $statedTournaments
            ->merge($coCastedTournaments)
            ->merge($mainCastedTournaments)
            ->merge($ownedTournaments);
        $tournaments = $tournaments->map(function ($item) use ($user) {
            $item->reference_id = $user->id;
            return $item;
        });

        return TournamentStateResource::collection($tournaments);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return Response
     * TODO Feature test
     * TODO Validate login and id
     */
    public function check_twitch_subscriber(User $user, Request $request): Response
    {
        if ($request->has("twitch_login"))
            $param = ["login" => $request->twitch_login];
        else if ($request->has("twitch_id"))
            $param = ["id" => $request->twitch_id];
        else
            $param = ["id" => User::find($request->user_id)->twitch_id ?? null];

        $userLookupResponse = Http::withHeaders(["Client-Id" => env("TWITCH_CLIENT_ID")])
            ->withToken(env("TWITCH_ACCESS_TOKEN"))
            ->get("https://api.twitch.tv/helix/users", $param);

        if ($userLookupResponse->status() != 200)
            return new Response($userLookupResponse->json(), $userLookupResponse->status());

        if (count($userLookupResponse->json("data")) <= 0)
            return new Response([
                "error" => "Not Found",
                "status" => 404,
                "message" => "The streamer was not found on Twitch",
            ], 404);

        $response = Http::withHeaders(["Client-Id" => env("TWITCH_CLIENT_ID")])
            ->withToken(env("TWITCH_ACCESS_TOKEN"))
            ->get("https://api.twitch.tv/helix/subscriptions/user?broadcaster_id=118368288&user_id=37264990", [
                "broadcaster_id" => $userLookupResponse->json("data.0.id"),
                "user_id" => $user->twitch_id,
            ]);

        return new Response($response->json(), $response->status());
    }
}
