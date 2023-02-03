<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Rule;

trait RuleSortBy
{
    public function getSortByRules(
        string $sortByName = 'sort_by',
        string $isDescName = 'is_desc',
        array $sortByKeys = []
    ): array {
        $rules[$sortByName][] = 'string';
        if (!empty($sortByKeys)) {
            $rules[$sortByName][] = Rule::in($sortByKeys);
        }
        $rules[$isDescName][] = 'boolean';

        return $rules;
    }

    public function getSortByMessages(string $sortByName = 'sort_by', string $isDescName = 'is_desc')
    {
        return [
            sprintf('%s.in', $sortByName) => ':attribute must be one of the following values: :values',
            sprintf('%s.*', $sortByName) => 'Incorrect sort format.',
            sprintf('%s.*', $isDescName) => 'Incorrect descending format.',
        ];
    }
}
