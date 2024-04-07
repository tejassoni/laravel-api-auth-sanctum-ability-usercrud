<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; // APIVALIDATIONRESPONSE
use Illuminate\Http\Exceptions\HttpResponseException; // APIVALIDATIONRESPONSE

class UserUpdateRequest extends FormRequest
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
        dd('Test 123',request()->all());
        return [
            'firstname' => ['required', 'alpha', 'max:100'],
            'lastname' => ['required', 'alpha', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email,'.request()->segment(4)],
            'mobile' => ['required', 'string', 'min:10', 'max:13', 'unique:users,mobile,'.request()->segment(4)],
            'address' => ['required', 'string'],
            'pincode' => ['required', 'integer', 'max:999999'],
            'country' => ['required', 'string'],
            'gender' => ['required', 'alpha', 'min:4', 'max:6'],
            'birthdate' => ['required', 'date_format:d-m-Y', 'before:today'],
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
