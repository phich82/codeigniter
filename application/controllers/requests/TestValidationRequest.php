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
            'username'     => 'required|min_length[5]|max_length[12]',
            'password'     => 'required|min_length[8]',
            'passconf'     => 'required|matches[password]',
            'email'        => 'is_email|in[jhphich@gmail.com,phich82@gmail.com]',
            'levels[]'     => 'min_length[5]|max_length[32]|nullable',
            'roles'        => 'array[roles]|nullable',
            'roles[role]'  => 'array[roles[role]:min[1]:max[2]:size[2]]|nullable', //roles[role] = roles.role
            'roles.role.*' => 'in_list[1,2,3,4]|nullable',
            'colors'       => 'array[colors:min[5]]|nullable',
            'colors.*'     => 'in_list[1,2,3,4]|nullable',
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
                'required' => 'Please enter username',
            ],
            'levels[0]' => [
                'min_length' => 'min length 5',
            ],
            'levels[0].max_length' => 'max length 32',
            'password.required' => 'Please enter password.',
            'passconf.required' => 'Please enter password confirmation.',
            'email.email' => 'Please enter an valid email.',
            'roles.array' => 'Role must be an array',
            // 'roles.role.array' => 'Role must be an array',
            'colors.*' => 'Color is a boolean value',
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
