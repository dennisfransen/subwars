<?php

namespace App\Http\Requests;

use App\Http\Enums\TournamentEntryLevel;
use App\Models\Tournament;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TournamentUpdateRequest extends FormRequest
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
            "title" => [
                Rule::unique(Tournament::class)->ignore($this->tournament->id),
            ],
            "max_teams" => [
                "numeric",
                "min:2",
            ],
            "min_elo" => [
                "numeric",
            ],
            "max_elo" => [
                "numeric",
            ],
            "visible_at" => [
                "date",
            ],
            "registration_open_at" => [
                "date",
            ],
            "check_in_open_at" => [
                "date",
            ],
            "live_at" => [
                "date",
            ],
            "entry_level" => [
                Rule::in((new TournamentEntryLevel())->getStringArray()),
            ],
            "prioritize_by_entry_level" => [
                "boolean",
            ],
        ];
    }
}
