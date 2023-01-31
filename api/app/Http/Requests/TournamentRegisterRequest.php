<?php

namespace App\Http\Requests;

use App\Http\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TournamentRegisterRequest extends FormRequest
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
                Rule::unique("tournament_user", "user_id")->where(function ($query) {
                    return $query->where("tournament_id", request()->route()->parameter("tournament")->id);
                }),
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    $esportalResponse = User::getEsportalUser($user->esportal_username);

                    if ($esportalResponse == null)
                        return $fail("The user does not exist on Esportal.");

                    request()->request->add(["esportal_elo" => $esportalResponse["elo"]]);

                    return true;
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
