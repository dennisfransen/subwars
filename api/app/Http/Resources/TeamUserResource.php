<?php

namespace App\Http\Resources;

use App\Http\Enums\TournamentEntryLevel;
use App\Http\Enums\TournamentUserState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamUserResource extends JsonResource
{
    public function withResponse($request, $response)
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
            "elo" => (int)$this->esportal_elo,
            "esportal_username" => $this->esportal_username,
            "locked" => $this->isLocked($this->tournament_id),
        ];
    }
}
