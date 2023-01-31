<?php

namespace App\Http\Requests;

use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserDetachSponsorRequest extends FormRequest
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
            "sponsor_id" => [
                function ($attribute, $value, $fail) {
                    if (!$this->user->sponsors()->where("id", $value)->count())
                        $fail("The sponsor is not attached to the user.");
                },
                "exists:sponsors,id",
            ],
        ];
    }
}
