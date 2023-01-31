<?php

namespace App\Http\Requests;

use App\Http\Enums\CasterRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TournamentAttachCoCasterRequest extends FormRequest
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
            "user_id" => [
                "exists:users,id",
            ],
            "role" => [
                Rule::in((new CasterRole())->getStringArray()),
            ],
        ];
    }
}
