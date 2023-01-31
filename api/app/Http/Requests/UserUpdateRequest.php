<?php

namespace App\Http\Requests;

use App\Http\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            "type" => [
                Rule::in((new UserType())->getStringArray()),
            ],
            "esportal_username" => [
                "nullable",
                Rule::unique(User::class)->ignore($this->user->id),
                function ($attribute, $value, $fail) {
                    $esportalResponse = $this->user->getEsportalUser($value);

                    if ($esportalResponse == null)
                        return $fail("The user does not exist on Esportal.");

                    request()->request->add(["esportal_elo" => $esportalResponse["elo"]]);

                    return true;
                },
            ],
        ];
    }
}
