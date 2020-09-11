<?php

namespace App\Params\Traits;

trait Sortable
{
    /**
     * sort by.
     *
     * @var array
     */
    private $sortBy;

    /**
     * Get sort by.
     *
     * @return array
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * Set sort by.
     *
     * @param string $sortBy sort by
     * @param bool   $isDesc
     *
     * @return self
     */
    public function setSortBy(string $sortBy, bool $isDesc = false)
    {
        $this->sortBy[$sortBy] = $isDesc;

        return $this;
    }
}
