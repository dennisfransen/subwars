<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentStatsResource extends JsonResource
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
        return [
            "id" => $this->id,
            "max_teams" => (int)$this->max_teams,
            "min_elo" => (int)$this->min_elo,
            "max_elo" => (int)$this->max_elo,
            "registered_count" => $this->registered()->count(),
            "checked_in_count" => $this->checkedIn()->count(),
        ];
    }
}
