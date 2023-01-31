<?php

namespace App\Http\Requests;

use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class TournamentCheckInRequest extends FormRequest
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
                function ($attribute, $value, $fail) {
                    $query = $this->tournament->users()->where("id", $value);

                    if (!$query->count())
                        $fail("The user is not registered.");
                    else {
                        $relation = $query->first();
                        if ($relation->pivot->state == TournamentUserState::CHECKED_IN)
                            $fail("The user is already checked in.");
                        elseif ($relation->pivot->state < 0)
                            $fail("The user is kicked.");
                        else {

                            $esportalResponse = User::getEsportalUser($relation->esportal_username);

                            if ($esportalResponse == null)
                                return $fail("The user does not exist on Esportal.");

                            request()->request->add(["esportal_elo" => $esportalResponse["elo"]]);
                        }
                    }
                },
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            "user_id" => $this->user_id ?? auth()->user()->id,
        ]);
    }
}
