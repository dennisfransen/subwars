<?php

namespace App\Http\Resources;

use App\Http\Enums\TournamentUserState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentActionsResource extends JsonResource
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
        $state = $this->users()->where("id", $request->user()->id)->first()->pivot->state ?? 0;

        return [
            "id" => $this->id,
            "state" => (new TournamentUserState())->getStringOfInteger($state),
            "is_open_for_registration" => (bool)$this->is_open_for_registration,
            "is_open_for_check_in" => (bool)$this->is_open_for_check_in,
        ];
    }
}
