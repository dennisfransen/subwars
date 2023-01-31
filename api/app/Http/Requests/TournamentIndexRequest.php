<?php

namespace App\Http\Requests;

use App\Http\Enums\TournamentEntryLevel;
use App\Models\Tournament;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TournamentIndexRequest extends FormRequest
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
            "min_live_at" => [
                "date",
            ],
            "max_live_at" => [
                "date",
            ],
        ];
    }
}
