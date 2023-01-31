<?php

namespace App\Http\Requests;

use App\Models\Tournament;
use Illuminate\Foundation\Http\FormRequest;

class AttachPlayerRequest extends FormRequest
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
        return [
            "user_id" => [
                function ($attribute, $value, $fail) {
                    if (!$this->team->tournament->reserve()->where("user_id", $value)->count())
                        $fail("The user is not in the reserve.");

                    if ($this->team->users()->count() >= Tournament::TEAM_PLAYER_COUNT)
                        $fail("The team is full.");
                }
            ],
        ];
    }
}
