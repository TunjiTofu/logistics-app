<?php

namespace App\Http\Requests\User\Shipment;

use App\Http\Requests\Utility\BaseFormRequest;

class CreateShipmentRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'senderName' => [
                'required',
                'string',
            ],
            'receiverName' => [
                'required',
                'string',
            ],
            'originAddress' => [
                'required',
                'string',
            ],
            'destinationAddress' => [
                'required',
                'string',
            ]
        ];
    }
}
