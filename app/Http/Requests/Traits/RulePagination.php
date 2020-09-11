<?php

namespace App\Http\Requests\Traits;

trait RulePagination
{
    /**
     * get pagination rules.
     *
     * @param string $pageName
     * @param string $perPageName
     *
     * @return array
     */
    public function getPaginationRules(string $pageName = 'page', string $perPageName = 'page_size')
    {
        return [
            $pageName => [
                'integer',
                'min:1',
            ],
            $perPageName => [
                'integer',
                sprintf('between:%s', implode(',', static::getPerPageRange())),
            ],
        ];
    }

    /**
     * get pagination messages.
     *
     * @param string $pageName
     * @param string $perPageName
     *
     * @return array
     */
    public function getPaginationMessages(string $pageName = 'page', string $perPageName = 'page_size')
    {
        return [
            sprintf('%s.min', $pageName) => 'The number of pages must be at least :min.',
            sprintf('%s.*', $pageName) => 'Incorrect page format.',
            sprintf('%s.between', $perPageName) => 'The number of page size must be between :min ~ :max.',
            sprintf('%s.*', $perPageName) => 'Incorrect page size format.',
        ];
    }

    /**
     * get per page range.
     *
     * @return array
     */
    public static function getPerPageRange()
    {
        return [1, config('app.max_page_size')];
    }
}
