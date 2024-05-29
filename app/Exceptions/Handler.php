<?php

namespace App\Exceptions;

use App\Enums\ApiResponseCode;
use App\Http\Traits\FormatJsonResponses;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    use FormatJsonResponses;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Throwable $exception)
    {
        // If the request wants JSON (AJAX doesn't always want JSON)
        if (!$request->wantsJson() || $exception instanceof CustomException) {
            return parent::render($request, $exception);
        }

        // check exceptions
        switch (get_class($exception)) {
            // 401
            case UnauthorizedHttpException::class:
            case AuthenticationException::class:
                $code = Response::HTTP_UNAUTHORIZED;
                $apiCode = ApiResponseCode::ERROR_UNAUTHORIZED;
                break;

            default:
                $code = Response::HTTP_BAD_REQUEST;
                $apiCode = ApiResponseCode::ERROR_UNEXPECTED;
                break;
        }

        return parent::render($request, app(CustomException::class, [
            'statusCode' => $code,
            'apiCode' => $apiCode,
        ]));
    }
}
