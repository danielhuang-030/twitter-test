<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse; // Added this line

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
    public function getJSON(): JsonResponse
    {
        $swagger = \OpenApi\Generator::scan([
            app_path('Http/Controllers/'),
        ]);

        return response()->json($swagger, 200);
    }
}
