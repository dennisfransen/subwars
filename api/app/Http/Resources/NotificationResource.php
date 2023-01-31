<?php

namespace App\Http\Resources;

use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = [
            "id" => $this->id,
            "description" => $this->description,
            "read_at" => $this->read_at,
        ];

        if ($this->notifiable_type === Tournament::class) {
            $data["tournament"] = new TournamentResource($this->notifiable);
        } elseif ($this->notifiable_type === Team::class) {
            $data["team"] = new TeamResource($this->notifiable);
        }

        return $data;
    }
}
