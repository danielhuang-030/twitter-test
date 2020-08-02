<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonRequest;

class LoginRequest extends JsonRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'       => [
                'required',
                'email',
            ],
            'password'    => [
                'required',
            ],
        ];
    }
}
