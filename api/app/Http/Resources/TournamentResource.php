<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentResource extends JsonResource
{
    /**
     * @param $request
     * @param $response
     * @return void
     */
    public function withResponse($request, $response): void
    {
        $response->setEncodingOptions(JSON_UNESCAPED_SLASHES);

        parent::withResponse($request, $response);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $this->whenLoaded("teams", function () {
            $userIds = $this->teams->pluck("users")->flatten(1)->pluck("id");
            $tournamentUsers = $this->users()->whereIn("id", $userIds)->withPivot("order");
            $orders = $tournamentUsers->get()->keyBy("id")->map(function ($user) {
                return $user->pivot->order;
            })->toArray();

            $this->teams = $this->teams->map(function ($team) use ($orders) {
                $team->users = $team->users->map(function ($user) use ($orders) {
                    $user->pivot->order = $orders[$user->id];
                    return $user;
                });
                return $team;
            });
        });

        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "image_url" => $this->image != "" ? asset("storage/" . $this->image) : "",
            "twitch_url" => $this->streamer_url,
            "rules" => $this->rules,
            "max_teams" => (int)$this->max_teams,
            "entry_level" => $this->entry_level,
            "prioritize_by_entry_level" => (bool)$this->prioritize_by_entry_level,
            "min_elo" => (int)$this->min_elo,
            "max_elo" => (int)$this->max_elo,
            "average_elo" => (int)$this->averageElo,
            "registered_count" => $this->registered()->count(),
            "checked_in_count" => $this->checkedIn()->count(),
            "visible_at" => $this->visible_at,
            "live_at" => $this->live_at,
            "registration_open_at" => $this->registration_open_at,
            "is_open_for_registration" => (bool)$this->is_open_for_registration,
            "is_open_for_check_in" => (bool)$this->is_open_for_check_in,
            "bracket" => $this->whenLoaded("bracket", function () {
                return new BracketResource($this->bracket);
            }),
            "sponsors" => $this->whenLoaded("sponsors", function () {
                return SponsorResource::collection($this->sponsors);
            }),
            "registered" => $this->whenLoaded("registered", function () {
                return RegisteredResource::collection($this->registered);
            }),
            "reserve" => $this->whenLoaded("reserve", function () {
                return RegisteredResource::collection($this->registeredAndCheckedIn);
            }),
            "creator" => $this->whenLoaded("creator", function () {
                return new UserSlimResource($this->creator);
            }),
            "teams" => $this->whenLoaded("teams", function () {
                return TeamResource::collection($this->teams);
            }),
            "prices" => $this->whenLoaded("prices", function () {
                return PriceResource::collection($this->prices);
            }),
            "co_casters" => $this->whenLoaded("coCasters", function () {
                return CoCasterResource::collection($this->coCasters);
            }),
        ];
    }
}
