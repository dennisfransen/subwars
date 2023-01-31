<?php

namespace App\Http\Resources;

use App\Http\Enums\FightTeamResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            "title" => $this->title,
            "average_elo" => $this->average_elo,
            "users" => $this->whenLoaded("users", function () {
                return TeamUserResource::collection($this->users->map(function ($item) {
                    $item->tournament_id = $this->tournament_id;
                    return $item;
                }));
            }),
            "result" => $this->whenPivotLoaded("fight_team", function () {
                switch ($this->pivot->result) {
                    case FightTeamResult::WIN:
                        return "Win";
                    case FightTeamResult::LOSS:
                        return "Loss";
                }

                return "-";
            }),
        ];
    }
}
