<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //TODO Fixa detta
        return [
            "username" => "required|unique:users",
            "esportal_username" => [
                "required",
                "unique:users,esportal_username",
                function ($attribute, $value, $fail) {
                    $esportalResponse = User::getEsportalUser($value);

                    if ($esportalResponse == null)
                        return $fail("The user does not exist on Esportal.");

                    request()->request->add(["esportal_elo" => $esportalResponse["elo"]]);

                    return true;
                },
            ],
        ];
    }
}
