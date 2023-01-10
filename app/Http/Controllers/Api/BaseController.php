<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\FormatJsonResponses;
use Illuminate\Http\JsonResponse;

abstract class BaseController extends Controller
{
    use FormatJsonResponses;

    public function __construct()
    {
        // stay flexible for parent construct
    }

    public static function replaceJsonResponseData(JsonResponse $jsonResponse, array $dataReplaced = []): JsonResponse
    {
        $responseData = $jsonResponse->getData(true);
        $responseData['data'] = array_merge($responseData['data'], $dataReplaced);

        return $jsonResponse->setData($responseData);
    }
}
