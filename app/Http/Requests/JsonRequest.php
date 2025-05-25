<?php

namespace App\Http\Requests;

use App\Enums\ApiResponseCode;
use App\Http\Traits\FormatJsonResponses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonRequest extends FormRequest
{
    use FormatJsonResponses;

    /**
     * Handle a failed validation attempt. (override).
     *
     * @param Validator $validator
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException($this->responseFail(code: ApiResponseCode::ERROR_VALIDATION->value, message: collect($validator->errors()->getMessages())->flatten()->implode("\n"), httpStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
