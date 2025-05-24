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
    protected array $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected array $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(\Illuminate\Http\Request $request, \Throwable $exception): \Symfony\Component\HttpFoundation\Response
    {
        // If the request wants JSON (AJAX doesn't always want JSON)
        if (!$request->wantsJson() || $exception instanceof CustomException) {
            return parent::render($request, $exception);
        }

        // check exceptions
        [$code, $apiCode] = match (get_class($exception)) {
            UnauthorizedHttpException::class, AuthenticationException::class => [Response::HTTP_UNAUTHORIZED, ApiResponseCode::ERROR_UNAUTHORIZED],
            default => [Response::HTTP_BAD_REQUEST, ApiResponseCode::ERROR_UNEXPECTED],
        };

        // Create a new CustomException instance to pass to the parent render method
        // This ensures that our custom rendering logic (if any within CustomException::render) is used.
        // Or, if the goal is just to use the FormatJsonResponses trait's methods,
        // we could directly call responseFail, but parent::render is often preferred for consistency.
        // The original code was passing an instance of CustomException to parent::render, which is unusual.
        // Usually, parent::render would handle the original exception or a mapped one.
        // Let's assume the intent was to re-throw a CustomException to be handled by its own render method,
        // or to ensure it's logged as a CustomException.
        // However, the original code passes it to parent::render, which might not use CustomException's render method.
        // A more standard approach for JSON APIs is to return a JSON response directly from here.

        // Given the original structure `return parent::render($request, app(CustomException::class, ...))`,
        // it seems it wants the parent handler to deal with a *new* CustomException.
        // This is a bit unorthodox. If the goal is to return a JSON response formatted by
        // FormatJsonResponses trait, we should call $this->responseFail() directly.

        // Let's stick to the original logic of creating a new CustomException for the parent,
        // but ensure it's actually what's intended.
        // If CustomException's render() is meant to be triggered, then throwing it would be more direct.
        // If parent::render is to format it, then this is fine.
        // The original code `return parent::render($request, app(CustomException::class, ...))` might be problematic
        // as `parent::render` will again check `wantsJson` and might not use the `CustomException::render` method.

        // A cleaner way to handle this for JSON API in the handler:
        return $this->responseFail(
            code: $apiCode->value, // Pass the string value of the enum case
            message: $apiCode->message(), // Get message from enum
            httpStatusCode: $code
        );
    }
}
