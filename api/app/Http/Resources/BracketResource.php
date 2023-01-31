<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BracketResource extends JsonResource
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
            "status" => $this->status,
            "statusString" => $this->getStatusString(),
            "tournaments" => $this->whenLoaded("tournaments", function () {
                return TournamentResource::collection($this->tournaments);
            }),
        ];
    }
}
