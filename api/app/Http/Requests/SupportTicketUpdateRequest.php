<?php

namespace App\Http\Requests;

use App\Http\Enums\SupportTicketPriority;
use App\Http\Enums\SupportTicketType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupportTicketUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "email" => [
                "email",
            ],
            "type" => [
                Rule::in([
                    SupportTicketType::UNSPECIFIED,
                    SupportTicketType::TECHNICAL,
                    SupportTicketType::BANNED,
                    SupportTicketType::MISSING_PRICE,
                ])
            ],
            "priority" => [
                Rule::in([
                    SupportTicketPriority::GUEST,
                    SupportTicketPriority::USER,
                    SupportTicketPriority::SPECIAL_USER,
                    SupportTicketPriority::CO_CASTER,
                    SupportTicketPriority::STREAMER,
                    SupportTicketPriority::ADMIN,
                ]),
            ],
            "description" => [
                "min:2",
            ],
        ];
    }
}
