<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Utility\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserSignupRequest extends BaseFormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'name')
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
            ],

            "role" => [
                'required',
                'in:admin,user'
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
//                ->symbols()
                ->uncompromised()
            ],
        ];
    }
}
