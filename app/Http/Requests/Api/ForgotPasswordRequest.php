<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; // APIVALIDATIONRESPONSE
use Illuminate\Http\Exceptions\HttpResponseException; // APIVALIDATIONRESPONSE
use Illuminate\Validation\Rules\Password; // DEFAULTPASSWORD

class ForgotPasswordRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:100'],
            'mobile' => ['required', 'string', 'min:10', 'max:13'],
            'old_password' => ['required', Password::defaults()], // DEFAULTPASSWORD
            'new_password' => ['required', Password::defaults()], // DEFAULTPASSWORD
            'confirm_new_password' => ['required', 'same:new_password']
        ];
    }
    /*
     * APIVALIDATIONRESPONSE
     * Api custom validation error response message
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ], 422));
    }
}
