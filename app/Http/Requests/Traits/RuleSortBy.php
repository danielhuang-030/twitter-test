<?php

namespace App\Http\Requests\Traits;

trait RuleSortBy
{
    /**
     * get sort by rules.
     *
     * @param string $sortByName
     * @param string $isDescName
     *
     * @return array
     */
    public function getSortByRules(string $sortByName = 'sort_by', string $isDescName = 'is_desc')
    {
        return [
            $sortByName => [
                'string',
            ],
            $isDescName => [
                'boolean',
            ],
        ];
    }

    /**
     * get sort by messages.
     *
     * @param string $sortByName
     * @param string $isDescName
     *
     * @return array
     */
    public function getSortByMessages(string $sortByName = 'sort_by', string $isDescName = 'is_desc')
    {
        return [
            sprintf('%s.*', $sortByName) => '排序格式错误',
            sprintf('%s.*', $isDescName) => '排序升降幂格式错误',
        ];
    }
}
