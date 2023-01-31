<?php

namespace App\Http\Requests;

use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TournamentDetachFromTeamsRequest extends FormRequest
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
                "array",
            ],
        ];
    }
}
