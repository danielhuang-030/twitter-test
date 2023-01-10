<?php

namespace App\Enums;

enum ApiResponseCode: string
{
    case SUCCESS = '000000';

    case ERROR_UNEXPECTED = '999999';
    case ERROR_VALIDATION = '999001';
    case ERROR_UNAUTHORIZED = '999002';

    public function message(): string
    {
        return match ($this) {
            static::SUCCESS => 'success',
            static::ERROR_UNEXPECTED => 'unexpected error',
            static::ERROR_VALIDATION => 'validation error',
            static::ERROR_UNAUTHORIZED => 'unauthorized',
            default => 'unexpected code',
        };
    }
}
