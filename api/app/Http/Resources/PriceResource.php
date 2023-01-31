<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
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
            "title" => $this->title,
            "image_url" => $this->image_url != "" ? asset("storage/" . $this->image_url) : "",
            "tournament" => $this->whenLoaded("tournament", function () {
                return new TournamentResource($this->tournament);
            }),
        ];
    }
}
