<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentTeamsResource extends JsonResource
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
            "average_elo" => (int)$this->averageElo,
            "registered" => $this->whenLoaded("registered", function () {
                return RegisteredResource::collection($this->registered);
            }),
            "reserve" => $this->whenLoaded("reserve", function () {
                return RegisteredResource::collection($this->registeredAndCheckedIn);
            }),
            "teams" => $this->whenLoaded("teams", function () {
                return TeamResource::collection($this->teams);
            }),
        ];
    }
}
