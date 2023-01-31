<?php

namespace App\Http\Requests;

use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TournamentSwitchPlayersRequest extends FormRequest
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
            "user_ids" => [
                "required",
                function ($attribute, $value, $fail) {
                    $firstUserId = $value[0] ?? null;
                    $secondUserId = $value[1] ?? null;

                    if (!$firstUserId || !$secondUserId)
                        return $fail("The first or second user id is missing.");

                    $firstUser = $this->tournament->users()->where("id", $firstUserId)->first();
                    if (!$firstUser)
                        return $fail("The first user is not connected to this tournament.");

                    if ($firstUser->pivot->state < TournamentUserState::CHECKED_IN)
                        return $fail("The first user is not checked in.");

                    $secondUser = $this->tournament->users()->where("id", $secondUserId)->first();
                    if (!$secondUser)
                        return $fail("The second user is not connected to this tournament.");

                    if ($secondUser->pivot->state < TournamentUserState::CHECKED_IN)
                        return $fail("The second user is not checked in.");

                    if (!$this->tournament->teams()->whereHas("users", function (Builder $query) use ($value) {
                        $query->whereIn("users.id", $value);
                    })->count())
                        return $fail("None of the users are placed in any team.");

                    return true;
                },
            ],
        ];
    }
}
