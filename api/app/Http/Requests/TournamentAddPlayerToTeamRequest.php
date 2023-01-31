<?php

namespace App\Http\Requests;

use App\Http\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;

class TournamentAddPlayerToTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        if ($this->user_id !== auth()->user()->id)
            return auth()->user()->type >= UserType::SUPERADMIN;

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
                "required",
                function ($attribute, $value, $fail) {
                    $query = $this->tournament->users()->where("id", $value);

                    if (!$query->count())
                        $fail("The user is not registered.");
                },
            ],
            "team_id" => [
                function ($attribute, $value, $fail) {
                    $query = $this->tournament->teams()->where("id", $value);

                    if (!$query->count())
                        $fail("The team is not a part of this tournament.");
                }
            ]
        ];
    }
}
