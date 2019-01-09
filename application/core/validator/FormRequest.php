<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'Validator.php';

/**
 * Validate the form from the incomming request.
 */
class FormRequest extends Validator
{
    protected $request;
    protected $input;
    public $error;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $input = new CI_Input();
        $this->input   = $input;
        $this->request = $input;
        // validate parameters from request
        $this->_validate();
    }

    /**
     * Validate form.
     *
     * @return void
     */
    private function _validate()
    {
        if ($this->authorize() === true) {
            // load the form_validation library
            $CI =& get_instance();
            $CI->load->library('form_validation');
            // set delimiter
            $CI->form_validation->set_error_delimiters(null, null);
            // validate form
            $CI->form_validation->set_rules($this->_getRules())->run();
            // track the error if any
            if (!empty($error = $CI->form_validation->error_array())) {
                $this->error = $error;
            }
        }
    }

    /**
     * Get rules.
     *
     * @return array
     */
    private function _getRules()
    {
        $messages   = $this->_getMessages();
        $attributes = $this->attributes();

        $config = [];
        foreach ($this->rules() as $field => $rules) {
            if (strpos($field, '.') === false) {
                $config[] = [
                    'field'  => $field,
                    'label'  => array_key_exists($field, $attributes) ? $attributes[$field] : null,
                    'rules'  => $rules,
                    'errors' => array_key_exists($field, $messages) ? $messages[$field] : []
                ];
            } else {
                $fields = $this->_getNestedArrayFields($field);
                foreach ($fields as $nestedField) {
                    $config[] = [
                        'field'  => $nestedField,
                        'label'  => array_key_exists($nestedField, $attributes) ? $attributes[$nestedField] : null,
                        'rules'  => $rules,
                        'errors' => array_key_exists($nestedField, $messages) ? $messages[$nestedField] : []
                    ];
                }
            }
        }
        return $config;
    }

    /**
     * Get the nested array rules: ['colors.4.color.*' => 'array']
     *
     * @param string $field  []
     * @param array  $params []
     *
     * @return array
     */
    private function _getNestedArrayFields($field, $params = [])
    {
        $params = !empty($params) ? $params : $this->input->get();
        $parts  = explode('.', $field);
        $firstElement = array_shift($parts);
        $rules = [$firstElement];
        $currentValue = $params[$firstElement];
    
        foreach ($parts as $part) {
            if ($part == '*') {
                $totalElements = count($currentValue);
                $currentValue  = $currentValue[0];
                $temp = [];
                for ($s=0; $s < $totalElements; $s++) {
                    foreach ($rules as $k => $rule) {
                        $temp[] = $rule."[".$s."]";
                    }
                }
                $rules = $temp;
            } else {
                $currentValue = $currentValue[$part];
                $temp = [];
                foreach ($rules as $k => $rule) {
                    $temp[] = $rule."[".$part."]";
                }
                $rules = $temp;
            }
        }
        return $rules;
    }

    /**
     * Get messages.
     *
     * @return array
     * @throws Exception
     */
    private function _getMessages()
    {
        $messages = [];
        $dot = '.';
        foreach ($this->messages() as $field_rule => $message) {
            if (is_array($message)) {
                if (strpos($field_rule, $dot) !== false) {
                    $this->_throwError($field_rule);
                }
                $messages[$field_rule] = $message;
            } elseif (is_string($message)) {
                if (strpos($field_rule, $dot) === false) {
                    $this->_throwError($field_rule);
                }
                $fieldRule = explode($dot, $field_rule);
                if (count($fieldRule) !== 2) {
                    $this->_throwError($field_rule);
                }
                $messages[$fieldRule[0]][$fieldRule[1]] = $message;
            } else {
                $this->_throwError($field_rule);
            }
        }
        return $messages;
    }

    /**
     * Check whether the error has occured
     *
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }

    /**
     * Get error
     *
     * @return array|null
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Throw exception
     *
     * @param string $field [field name of rule]
     *
     * @return void
     * @throws Exception
     */
    private function _throwError($field)
    {
        throw new Exception("Field [$field] is not correct.");
    }
}
