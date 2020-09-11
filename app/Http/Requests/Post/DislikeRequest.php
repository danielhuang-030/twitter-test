<?php

namespace App\Http\Requests\Follow;

use App\Http\Requests\JsonRequest;
use App\Http\Requests\Traits\MergeRouteParams;

class DislikeRequest extends JsonRequest
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
