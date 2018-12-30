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
            // validate form
            $CI->form_validation->set_rules($this->_getRules())->run();
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
            $config[] = [
                'field'  => $field,
                'label'  => array_key_exists($field, $attributes) ? $attributes[$field] : null,
                'rules'  => $rules,
                'errors' => array_key_exists($field, $messages) ? $messages[$field] : []
            ];
        }
        return $config;
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
            } else if (is_string($message)) {
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
