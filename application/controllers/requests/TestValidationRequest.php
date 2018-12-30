<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
            'username' => 'required|min_length[5]|max_length[12]',
            'password' => 'required|min_length[8]',
            'passconf' => 'required|matches[password]',
            'email'    => 'is_email|in[jhphich@gmail.com,phich82@gmail.com]'
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
