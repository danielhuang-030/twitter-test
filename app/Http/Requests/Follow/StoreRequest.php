<?php

namespace App\Http\Requests\Follow;

use App\Http\Requests\JsonRequest;

class StoreRequest extends JsonRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => [
                'required',
                'exists:users',
            ],
        ];
    }
}
