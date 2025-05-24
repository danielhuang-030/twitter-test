<?php

namespace App\Http\Traits;

use App\Enums\ApiResponseCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

trait FormatJsonResponses
{
    /**
     * response success.
     *
     * @param array  $data
     * @param string $code
     * @param string $message
     * @param int    $httpStatusCode
     *
     * @return JsonResponse
     */
    protected function responseSuccess(
        array $data = [],
        string $code = '',
        string $message = '',
        int $httpStatusCode = Response::HTTP_OK
    ): JsonResponse {
        return response()->json(data: [
            'code' => $code = (!empty($code) ? $code : ApiResponseCode::SUCCESS->value),
            'message' => !empty($message) ? $message : ApiResponseCode::from($code)?->message(),
            'data' => !empty($data) ? $data : (object) $data,
        ], status: $httpStatusCode, options: \JSON_UNESCAPED_UNICODE);
    }

    /**
     * response success with pagination.
     *
     * @param LengthAwarePaginator $paginator
     * @param array                $data
     * @param string               $code
     * @param string               $message
     * @param int                  $httpStatusCode
     *
     * @return JsonResponse
     */
    protected function responseSuccessWithPagination(
        LengthAwarePaginator $paginator,
        array $data = [],
        array $extraData = [],
        string $code = '',
        string $message = '',
        int $httpStatusCode = Response::HTTP_OK
    ): JsonResponse {
        $responseData = [
            'data' => $data,
        ];
        if (!empty($extraData)) {
            $responseData = array_merge($responseData, $extraData);
        }

        return $this->responseSuccess(
            array_merge([
                'pagination' => [
                    'page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ], $responseData),
            $code,
            $message,
            $httpStatusCode
        );
    }

    /**
     * response fail.
     *
     * @param string $code
     * @param string $message
     * @param int    $httpStatusCode
     *
     * @return JsonResponse
     */
    protected function responseFail(
        array $data = [],
        string $code = '',
        string $message = '',
        int $httpStatusCode = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        return response()->json(data: [
            'code' => $code = (!empty($code) ? $code : ApiResponseCode::ERROR_UNEXPECTED->value),
            'message' => !empty($message) ? $message : ApiResponseCode::from($code)?->message(),
            'data' => !empty($data) ? $data : (object) $data,
        ], status: $httpStatusCode, options: JSON_UNESCAPED_UNICODE);
    }
}
