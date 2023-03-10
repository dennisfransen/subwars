<?php

namespace App\Http\Requests;

use App\Models\Bracket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PriceUpdateRequest extends FormRequest
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
            "tournament_id" => [
                "exists:tournaments,id",
            ],
            "title" => [
                "min:2",
            ],
        ];
    }
}
