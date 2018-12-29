<?php

require 'application/core/validator/FormRequest.php';

class TestValidationRequest extends FormRequest
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
            'username' => 'required|min_length[5]',
            'password' => 'required',
            'passconf' => 'required',
            'email'    => 'email'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username' => [
                'required' => 'Please enter username'
            ],
            'password.required' => 'Please enter password.',
            'passconf.required' => 'Please enter password confirmation.',
            'email.email' => 'Please enter an valid email.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'username' => 'UserName',
            'password' => 'Password',
            'passconf' => 'Password Confirmation',
            'email'    => 'Email'
        ];
    }
}
