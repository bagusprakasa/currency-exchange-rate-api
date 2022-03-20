<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users,email|email',
            'password' => 'required|min:6'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors(); // Here is your array of errors
        $response = response()->json([
            'status' => 'error',
            'message' => $errors->messages(),
        ], 422);

        throw new HttpResponseException($response);
    }
}
