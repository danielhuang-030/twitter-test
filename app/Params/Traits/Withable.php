<?php

namespace App\Params\Traits;

trait Withable
{
    /**
     * withs.
     *
     * @var array
     */
    private $withs;

    /**
     * Get withs.
     *
     * @return array
     */
    public function getWiths()
    {
        return $this->withs;
    }

    /**
     * Set withs.
     *
     * @param array $withs withs
     *
     * @return self
     */
    public function setWiths($withs)
    {
        $this->withs = $withs;

        return $this;
    }
}
