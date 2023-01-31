<?php

namespace App\Http\Requests;

use App\Http\Enums\SupportTicketType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupportTicketStoreRequest extends FormRequest
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
        $rules = [
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
            "description" => [
                "min:2",
                "required",
            ],
        ];

        if (!auth()->check())
            $rules["email"][] = "required";

        return $rules;
    }
}
