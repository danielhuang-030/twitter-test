<?php

namespace App\Http\Middleware;

use App\Enums\ApiResponseCode;
use App\Http\Traits\FormatJsonResponses;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    use FormatJsonResponses;

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     *
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        try {
            parent::authenticate($request, $guards);
        } catch (\Throwable $th) {
            if ($request->expectsJson()) {
                $code = ApiResponseCode::ERROR_UNAUTHORIZED->value;

                throw new HttpResponseException($this->responseFail(
                    code: $code,
                    message: ApiResponseCode::from($code)?->message(),
                    httpStatusCode: Response::HTTP_UNAUTHORIZED
                ));
            }

            throw $th;
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
