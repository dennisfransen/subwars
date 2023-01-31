<?php

namespace App\Http\Resources;

use App\Http\Enums\CasterRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoCasterResource extends JsonResource
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
            "username" => $this->username,
            "esportal_username" => $this->esportal_username,
            "role" => (new CasterRole())->getStringOfInteger($this->pivot->role ?? 0),
            "steam_avatar" => $this->avatar,
            "steam_avatar_medium" => $this->getAvatarWithSuffix("_medium"),
            "steam_avatar_full" => $this->getAvatarWithSuffix("_full"),
        ];
    }
}
