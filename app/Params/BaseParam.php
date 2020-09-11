<?php

namespace App\Params;

abstract class BaseParam
{
    public function toArray(): array
    {
        $array = [];

        $properties = get_object_vars($this);

        foreach ($properties as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }
}
