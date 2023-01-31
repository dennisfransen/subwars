<?php

namespace App\Http\Requests;

use App\Models\Bracket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PriceStoreRequest extends FormRequest
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
                "required",
            ],
            "tournament_id" => [
                "required",
                "exists:tournaments,id",
            ],
        ];
    }
}
