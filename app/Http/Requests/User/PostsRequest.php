<?php

namespace App\Http\Requests\User;

use App\Http\Requests\JsonRequest;
use App\Http\Requests\Traits\MergeRouteParams;
use App\Http\Requests\Traits\RulePagination;
use App\Http\Requests\Traits\RuleSortBy;

class PostsRequest extends JsonRequest
{
    use RulePagination;
    use RuleSortBy;
    use MergeRouteParams;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge_recursive(
            $this->getPaginationRules(),
            $this->getSortByRules(), [
                'id' => [
                    'required',
                    'exists:users',
                ],
            ]
        );
    }

    /**
     * messages.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge_recursive(
            $this->getPaginationMessages(),
            $this->getSortByMessages()
        );
    }
}
