<?php

namespace App\Http\Requests\Api\v1\Post;

use App\Http\Requests\JsonRequest;
use App\Http\Requests\Traits\MergeRouteParams;

class ShowRequest extends JsonRequest
{
    use MergeRouteParams;

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
}
