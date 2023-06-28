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
        protected $statusCode = Response::HTTP_BAD_REQUEST,
        protected $message = '',
        protected ApiResponseCode $apiCode = ApiResponseCode::ERROR_UNEXPECTED,
        protected $data = null
    ) {
        if (empty($message)) {
            $message = $this->apiCode->message();
        }

        parent::__construct($statusCode, $message);
    }

    public function render()
    {
        return $this->responseFail(
            data: $this->data,
            code: $this->apiCode->value,
            message: $this->apiCode->message(),
            httpStatusCode: $this->statusCode
        );
    }
}
