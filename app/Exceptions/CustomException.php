<?php

namespace App\Exceptions;

use App\Enums\ApiResponseCode;
use App\Http\Traits\FormatJsonResponses;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomException extends HttpException
{
    use FormatJsonResponses;

    public function __construct(
        protected readonly int $statusCode = Response::HTTP_BAD_REQUEST,
        protected readonly string $message = '',
        protected readonly ApiResponseCode $apiCode = ApiResponseCode::ERROR_UNEXPECTED,
        protected readonly ?array $data = null // Assuming data can be an array or null
    ) {
        // Message assignment logic needs to be handled carefully with readonly properties.
        // If $message is truly readonly, it cannot be reassigned after construction.
        // The current logic assigns to a local $message if the input $message is empty.
        // This local $message is then passed to parent::__construct.
        // So, the readonly property $this->message will hold the initial value passed to the constructor.
        // This seems fine as the effective message for the HttpException is what's passed to parent::__construct.

        $effectiveMessage = $message;
        if (empty($effectiveMessage)) {
            $effectiveMessage = $this->apiCode->message();
        }

        parent::__construct($this->statusCode, $effectiveMessage);
    }

    public function render(): \Illuminate\Http\JsonResponse
    {
        return $this->responseFail(
            data: $this->data,
            code: $this->apiCode->value,
            message: $this->apiCode->message(),
            httpStatusCode: $this->statusCode
        );
    }
}
