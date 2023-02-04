<?php

namespace App\Http\Requests\Api\v1\User;

use App\Http\Requests\JsonRequest;
use App\Http\Requests\Traits\MergeRouteParams;
use App\Http\Requests\Traits\RulePagination;
use App\Http\Requests\Traits\RuleSortBy;

class PostsRequest extends JsonRequest
{
    use RulePagination;
    use RuleSortBy;
    use MergeRouteParams;

    public const SORT_BY_CREATED_AT = 'created_at';
    public const SORT_BY_UPDATED_AT = 'updated_at';
    public const SORT_BY_AUTHOR = 'author';
    public const SORT_BY_KEYS = [
        PostsRequest::SORT_BY_CREATED_AT,
        PostsRequest::SORT_BY_UPDATED_AT,
        PostsRequest::SORT_BY_AUTHOR,
    ];

    public function rules(): array
    {
        return array_merge(
            $this->getPaginationRules(),
            $this->getSortByRules(sortByKeys: static::SORT_BY_KEYS), [
                'id' => [
                    'required',
                    'exists:users',
                ],
            ]
        );
    }

    public function messages(): array
    {
        return array_merge(
            $this->getPaginationMessages(),
            $this->getSortByMessages()
        );
    }
}
