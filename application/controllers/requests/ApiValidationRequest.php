<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'application/core/validator/FormRequest.php';

class ApiValidationRequest extends FormRequest
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
            'roles' => 'array[min[1]:max[2]:size[2]]',
            //'roles.*.name' => 'in_list[A,B]',
            //'roles.*.name' => 'integer',
            //'colors.*.color.*' => 'in_list[A,B]',
            'colors.*.color' => 'array[min[1]:max[2]]',
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
            //'roles.array' => 'Role must be an array.',
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
            //'roles' => 'Roles',
        ];
    }
}
