<?php

namespace App\Http\Resources;

use App\Http\Enums\CasterState;
use App\Http\Enums\TournamentUserState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentStateResource extends JsonResource
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
            "state" => (new TournamentUserState())->getStringOfInteger($this->pivot->state ?? 0),
            "caster" => (new CasterState())->getStringOfInteger($this->getCasterStatus($this->reference_id)),
        ];
    }
}
