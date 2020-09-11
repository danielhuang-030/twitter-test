<?php

namespace App\Params\Traits;

trait Pageable
{
    /**
     * page.
     *
     * @var int
     */
    private $page = 1;

    /**
     * per page.
     *
     * @var int
     */
    private $perPage = 10;

    /**
     * Get page.
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set page.
     *
     * @param int $page page
     *
     * @return self
     */
    public function setPage(int $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Set per page.
     *
     * @param int $perPage per page
     *
     * @return self
     */
    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }
}
