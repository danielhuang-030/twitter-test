<?php

namespace App\Http\Requests\Api\v1\Post;

use App\Http\Requests\JsonRequest;
use App\Http\Requests\Traits\MergeRouteParams;

class LikeRequest extends JsonRequest
{
    use MergeRouteParams;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'exists:posts',
            ],
        ];
    }
}
