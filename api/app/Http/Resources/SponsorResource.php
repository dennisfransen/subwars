<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SponsorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "image_url" => $this->image_url != "" ? asset("storage/" . $this->image_url) : "",
            "tournaments" => $this->whenLoaded("tournaments", function () {
                return TournamentResource::collection($this->tournaments);
            }),
        ];
    }
}
