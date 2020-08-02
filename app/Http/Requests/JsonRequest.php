<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class JsonRequest extends FormRequest
{
    /**
     * failed validation
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        throw new HttpResponseException(
            response()->json([
                'message' => $errors->first(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
