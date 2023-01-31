<?php

namespace App\Http\Requests;

use App\Models\Bracket;
use App\Models\Sponsor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SponsorUpdateRequest extends FormRequest
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
                "min:2",
                Rule::unique(Sponsor::class)->ignore($this->sponsor->id),
            ],
        ];
    }
}
