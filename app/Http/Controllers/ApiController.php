<?php

namespace App\Http\Controllers;

class ApiController extends Controller
{
    /**
     * getJSON.
     *
     * @OA\Info(
     *     title="twitter test API",
     *     description="Swagger for twitter test API",
     *     version="1.0",
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJSON()
    {
        $swagger = \OpenApi\scan(app_path('Http/Controllers/'));

        return response()->json($swagger, 200);
    }
}
