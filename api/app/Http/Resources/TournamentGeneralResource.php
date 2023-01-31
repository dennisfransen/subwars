<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentGeneralResource extends JsonResource
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
            "title" => $this->title,
            "description" => $this->description,
            "twitch_url" => $this->streamer_url,
            "sponsors" => $this->whenLoaded("sponsors", function () {
                return SponsorResource::collection($this->sponsors);
            }),
            "creator" => $this->whenLoaded("creator", function () {
                return new UserSlimResource($this->creator);
            }),
        ];
    }
}
