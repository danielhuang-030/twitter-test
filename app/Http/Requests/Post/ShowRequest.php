<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\JsonRequest;

class ShowRequest extends JsonRequest
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
                'exists:posts',
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