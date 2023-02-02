<?php

namespace App\Http\Requests\Api\v1\Post;

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
            'content' => [
                'required',
                'string',
            ],
        ];
    }
}
