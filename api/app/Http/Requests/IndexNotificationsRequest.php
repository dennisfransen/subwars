<?php

namespace App\Http\Requests;

use App\Http\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;

class IndexNotificationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth()->user()->type >= UserType::SUPERADMIN)
            return true;

        if (request()->has("user_id"))
            return request()->user_id == auth()->user()->id;

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
