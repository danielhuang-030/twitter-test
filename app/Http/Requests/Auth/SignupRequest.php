<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonRequest;

class SignupRequest extends JsonRequest
{
    /**
     * password pattern
     *
     * @var string
     */
    const PASSWORD_PATTERN = '#^\w{6,200}$#';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => [
                'required',
            ],
            'email'    => [
                'required',
                'email',
                'unique:users',
            ],
            'password' => [
                'required',
                sprintf('regex:%s', static::PASSWORD_PATTERN),
                'confirmed',
            ],
            'password_confirmation' => [
                'required',
            ]
        ];
    }
}
