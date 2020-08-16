<?php

namespace App\Http\Requests\Follow;

use App\Http\Requests\JsonRequest;

class UnfollowRequest extends JsonRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => [
                'required',
                'exists:users',
            ],
        ];
    }

    /**
     * validation data.
     *
     * @return array
     */
    public function validationData()
    {
        return array_merge($this->route()->parameters, parent::validationData());
    }
}
