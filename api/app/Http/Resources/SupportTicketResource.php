<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource
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
            "sender" => $this->whenLoaded("sender", function () {
                return new UserCompleteResource($this->sender);
            }),
            "email" => $this->email,
            "responder" => $this->whenLoaded("responder", function () {
                return new UserSimpleResource($this->responder);
            }),
            "description" => $this->description,
            "unread" => $this->unread,
            "type" => $this->type,
            "priority" => $this->priority,
        ];
    }
}
