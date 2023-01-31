<?php

namespace App\Http\Requests;

use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class TournamentKickPlayerRequest extends FormRequest
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
                "required",
                function ($attribute, $value, $fail) {
                    $query = $this->tournament->users()->where("id", $value);

                    if (!$query->count())
                        $fail("The user is not registered.");
                    else {
                        $relation = $query->first();
                        if ($relation->pivot->state < 0)
                            $fail("The user is already kicked.");
                    }
                },
            ],
        ];
    }
}
