<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "child_id" => $this->child_id,
            "round" => $this->round,
            "number" => $this->number,
            "tournament" => $this->whenLoaded("tournament", function () {
                return new TournamentResource($this->tournament);
            }),
            "teams" => $this->whenLoaded("teams", function () {
                return TeamResource::collection($this->teams);
            }),
            "parents" => FightResource::collection($this->parents->load(["teams"])),
        ];
    }
}
