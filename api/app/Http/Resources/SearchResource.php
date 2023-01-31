<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
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
            "tournaments" => TournamentResource::collection($this->tournaments),
            "streamers" => UserSimpleResource::collection($this->streamers),
        ];
    }
}
