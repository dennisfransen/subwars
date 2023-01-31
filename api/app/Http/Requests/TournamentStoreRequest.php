<?php

namespace App\Http\Requests;

use App\Http\Enums\TournamentEntryLevel;
use App\Models\Tournament;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TournamentStoreRequest extends FormRequest
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
                Rule::unique(Tournament::class),
                "required",
            ],
            "description" => [
                "required",
            ],
            "rules" => [
                "required",
            ],
            "max_teams" => [
                "numeric",
                "required",
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
                "required",
            ],
            "registration_open_at" => [
                "date",
                "required",
            ],
            "check_in_open_at" => [
                "date",
            ],
            "live_at" => [
                "date",
                "required",
            ],
            "entry_level" => [
                Rule::in((new TournamentEntryLevel())->getStringArray()),
            ],
            "prioritize_by_entry_level" => [
                "boolean",
            ],
        ];
    }

    /**
     * @return Validator
     */
    protected function getValidatorInstance(): Validator
    {
        $validator = parent::getValidatorInstance();

        $validator->sometimes("max_elo", "gte:min_elo", function ($input) {
            return $input->min_elo ?? null;
        });

        return $validator;
    }
}
