<?php

namespace App\Http\Requests;

use App\Http\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;

class TournamentDeRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        if ($this->user_id !== auth()->user()->id)
            return auth()->user()->type >= UserType::SUPERADMIN;

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
            "user_id" => [
                function ($attribute, $value, $fail) {
                    $query = $this->tournament->users()->where("id", $value);

                    if (!$query->count())
                        $fail("The user is not registered.");
                },
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            "user_id" => $this->user_id ?? auth()->user()->id,
        ]);
    }
}
