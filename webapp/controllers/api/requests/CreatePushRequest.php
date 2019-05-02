<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) . '/../../../core/validator/FormRequest.php');

class CreatePushRequest extends FormRequest
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
        $rules = [
            'X-API-KEY'           => 'required', // header
            'company_code'        => 'required|max_length[6]', // header
            'store_code'          => 'required|max_length[3]', // header
        ];
        // for insert many
        if (is_array($this->params['data']) && count($this->params['data']) > 0) {
            return array_merge($rules, $this->_changeFieldsOfRules($this->_rulesGiven(), 'data.*.'));
        }
        // for insert one
        return array_merge($rules, $this->_rulesGiven());
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * Change fields of the given rules
     *
     * @param  array $rules
     * @param  string $prefix
     * @param  string $postfix
     *
     * @return array
     */
    private function _changeFieldsOfRules($rules = [], $prefix = '', $postfix = '')
    {
        $rulesFieldsChanged = [];
        foreach ($rules as $field => $rule) {
            $rulesFieldsChanged[$prefix.$field.$postfix] = $rule;
        }
        return $rulesFieldsChanged;
    }

    /**
     * Get the defined rules
     *
     * @return array
     */
    private function _rulesGiven()
    {
        return [
            'company'             => 'required|max_length[6]',
            'store'               => 'required|integer',
            'notification_type'   => 'integer',
            'before_sending_time' => 'numeric'
        ];
    }
}
