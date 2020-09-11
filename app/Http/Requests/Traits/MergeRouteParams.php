<?php

namespace App\Http\Requests\Traits;

trait MergeRouteParams
{
    /**
     * validation data.
     *
     * @return array
     */
    public function validationData()
    {
        return array_merge($this->route()->parameters, parent::validationData());
    }
}
