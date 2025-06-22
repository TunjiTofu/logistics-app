<?php

namespace App\Http\Requests\Utility;

use App\Traits\JsonResponseAPI;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BaseFormRequest extends FormRequest
{
    use JsonResponseAPI;

    /**
     *
     * @return void
     */
    public function handleContentTypeHeaderValidation(): void
    {
        # validate header
        if (! $this->hasHeader('Content-Type') || $this->header('Content-Type') !== 'application/json') {
            throw new HttpResponseException($this->errorResponse(
                'Include Content-Type and set the value to: application/json in your header.',
                ResponseAlias::HTTP_BAD_REQUEST
            ));
        }
    }

    /**
     * THis overrides the default throwable failed message in json format
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        Log::error("Form validation error", [$validator->errors()]);
        throw new HttpResponseException(
            $this->errorResponse(
                $validator->errors()->first(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
            )
        );
    }

    /**
     * @return string[]
     */
    public function validateEmail(): array
    {
        return [
            'required',
            'regex:/(.+)@(.+)\.(.+)/i',
        ];
    }


    /**
     * @return array
     */
    public function validateAmount(): array
    {
        return [
            'required',
            'numeric',
            'min:50'
        ];
    }
}
