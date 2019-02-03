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
    private $CI;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct($data = [])
    {
        $input = new CI_Input();
        $this->input   = $input;
        $this->request = $input;

        // validate parameters from request
        $this->_validate($data);
    }

    /**
     * Validate form.
     *
     * @param  array $data
     * @return void
     */
    private function _validate($data)
    {
        if ($this->authorize() === true) {
            // load the form_validation library
            $CI =& get_instance();
            $CI->load->library('form_validation');
            // set the custom data if any
            if (!empty($data)) {
                $CI->form_validation->set_data($data);
            }
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
     * @param array $customData
     * @return array
     */
    private function _getRules()
    {
        $messages   = $this->_getMessages();
        $attributes = $this->attributes();
        
        $config = [];
        foreach ($this->rules() as $field => $rules) {
            if (strpos($field, '.') === false) {
                $configRule = [
                    'field'  => $field,
                    'label'  => $attributes[$field] ?? null,
                    'rules'  => $rules,
                    'errors' => $messages[$field] ?? null,
                ];

                // check the rule of array and the subrules of array if any
                $arrayRule = $this->_getArrayRule($field, $rules);
                if (!empty($arrayRule)) {
                    $configRule['rules'] = [$arrayRule];
                }

                $config[] = $configRule;
            } else {
                // $fields = $this->_getNestedArrayFields($field, $customData);
                // foreach ($fields as $nestedField) {
                //     $config[] = [
                //         'field'  => $nestedField,
                //         'label'  => array_key_exists($nestedField, $attributes) ? $attributes[$nestedField] : null,
                //         'rules'  => $rules,
                //         'errors' => array_key_exists($nestedField, $messages) ? $messages[$nestedField] : []
                //     ];
                // }
            }
        }
        return $config;
    }

    /**
     * Get the nested array rules: ['colors.4.color.*' => 'array']
     *
     * @param string $field
     * @param array  $params
     *
     * @return array
     */
    private function _getNestedArrayFields($field, $params = [])
    {
        $params = !empty($params) ? $params : array_merge($this->input->post(), $this->input->get());
        $parts  = explode('.', $field);
        $firstElement = array_shift($parts);
        $rules = [$firstElement];

        // for case, inputs are checkboxes
        if (!array_key_exists($firstElement, $params)) {
            return [];
        }

        $currentValue = $params[$firstElement];
    
        foreach ($parts as $part) {
            if ($part == '*') {
                // key not exist
                if (empty(array_keys($currentValue)) || !isset($currentValue[array_keys($currentValue)[0]])) {
                    return [];
                }

                $totalElements = count($currentValue);
                $currentValue  = $currentValue[array_keys($currentValue)[0]];
                $temp = [];
                for ($s=0; $s < $totalElements; $s++) {
                    foreach ($rules as $rule) {
                        $temp[] = $rule."[".$s."]";
                    }
                }
                $rules = $temp;
            } else {
                // key not exist
                if (!isset($currentValue[$part])) {
                    return [];
                }

                $currentValue = $currentValue[$part];
                $temp = [];
                foreach ($rules as $rule) {
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
            // in case the fields are not nested
            if (substr_count($field_rule, $dot) < 2) {
                if (is_array($message) && strpos($field_rule, $dot) === false) {
                    $messages[$field_rule] = $message;
                } elseif (is_string($message) && strpos($field_rule, $dot) !== false) {
                    $fieldRule = explode($dot, $field_rule);
                    if (count($fieldRule) !== 2) {
                        $this->_throwError($field_rule);
                    }
                    $messages[$fieldRule[0]][$fieldRule[1]] = $message;
                } else {
                    $this->_throwError($field_rule);
                }
            } else { // in case the nested fields
                $parts = explode($dot, $field_rule);
                $rule = array_pop($parts);

                // missing the rule
                if (in_array($rule, ['.', '*'])) {
                    $this->_throwError($field_rule);
                }

                $nestedFields = $this->_getNestedArrayFields(implode($dot, $parts));

                foreach ($nestedFields as $nestedField) {
                    if (is_array($message)) {
                        $messages[$nestedField] = $message;
                    } elseif (is_string($message)) {
                        $messages[$nestedField][$rule] = $message;
                    } else {
                        $this->_throwError($field_rule);
                    }
                }
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
        throw new Exception("Field [$field] is invalid.");
    }

    /**
     * Validate the value can be null.
     *
     * @param string|null $value
     *
     * @return bool
     */
    private function nullable($value)
    {
        return is_null($value) || $value === '';
    }

    /**
     * Set the rule of array and the subrules of array
     *
     * @param  string $field
     * @param  array  $rules
     *
     * @return void
     */
    private function _getArrayRule($field, $rules)
    {
        $ruleName = 'array';
        // check the rule of array if found
        $arrayRule = array_filter(explode('|', $rules), function ($item) use ($ruleName) {
            return substr($item, 0, strlen($ruleName)) == $ruleName;
        });

        if (!empty($arrayRule)) {
            $CI =& get_instance();
            $CI->load->library('form_validation');

            $data = !empty($CI->form_validation->validation_data) ? $CI->form_validation->validation_data : array_merge($CI->input->post(), $CI->input->get());

            $subrules = $this->_extractSubRules($this->_getSubRules($arrayRule));

            return [$ruleName, function ($value) use ($data, $field, $subrules, $CI) {
                // key not found
                if (!array_key_exists($field, $data)) {
                    $CI->form_validation->set_message($ruleName, 'The {field} field is not found.');
                    return false;
                }
                // check it is an array
                if (!is_array($data[$field])) {
                    $CI->form_validation->set_message($ruleName, 'The {field} field must be an array.');
                    return false;
                }
                // validate the subrules of array (min, max, size) if found
                return $this->_validateArraySubrules($data[$field], $subrules, $CI);
            }];
        }
        return null;
    }

    /**
     * Get the sub rules from a string
     *
     * @param array|string $arrayRule
     *
     * @return array
     */
    private function _getSubRules($arrayRule = [])
    {
        $arrayRule = is_array($arrayRule) ? $arrayRule[0] : $arrayRule;
        $subrules  = [];
        if (preg_match('#^(?P<rule>\w+)\[(?P<subrule>.*)\]$#', $arrayRule, $matches) === 1 && isset($matches['subrule'])) {
            $subrules = explode(':', $matches['subrule']);
        }
        return $subrules;
    }

    /**
     * Validate the sub rules of array (min, max, size) if any
     *
     * @param string $field
     * @param mixed $valueChecked
     * @param array $subRules
     * @return bool
     */
    private function _validateArraySubrules($valueChecked, $subrules = [], $CI = null)
    {
        $ruleName = 'array';

        // the checked value is not an array
        if (!is_array($valueChecked)) {
            $this->set_message($ruleName, 'The {field} field must be an array.');
            return false;
        }

        $CI = is_object($CI) ? $CI : $this->CI;
        
        // check the sub rules of array (min, max, size) if any
        foreach ($subrules as $subrule) {
            // the subrule method not found
            if (!method_exists($this, $subrule['method'])) {
                $CI->form_validation->set_message($ruleName, 'The rule ['.$subrule['method'].'] for the {field} field is not found.');
                return false;
            }
            // validate the subrule of array
            if (!$this->{$subrule['method']}($valueChecked, $subrule['size'])) {
                $CI->form_validation->set_message($ruleName, $this->_getMsgErrorBySubrule($subrule['method'], $subrule['size']));
                return false;
            }
        }
        return true;
    }

    /**
     * Extract the sub rules if any
     *
     * @param array $rulesArray
     * @return array
     */
    private function _extractSubRules($rulesArray = [])
    {
        $subrules = [];
        $rulesArray = is_array($rulesArray) ? $rulesArray : [];
        foreach ($rulesArray as $rule) {
            if (preg_match('#^(?P<method>\w+)\[(?P<size>\d+)\]$#', $rule, $matches) === 1) {
                $subrules[] = [
                    'method' => $matches['method'],
                    'size'   => (int)$matches['size']
                ];
            }
        }
        return $subrules;
    }

    /**
     * Get the error message by the sub rule of array
     *
     * @param string $method
     * @param int $size
     *
     * @return string
     */
    private function _getMsgErrorBySubrule($method, $size)
    {
        switch ($method) {
            case 'min':
                $msgError = 'The {field} field must have at least '.$size.'.';
                break;
            case 'max':
                $msgError = 'The {field} field must have at maxium '.$size.'.';
                break;
            case 'size':
                $msgError = 'The {field} field must have the size as '.$size.'.';
                break;
            default:
                $msgError = 'Rule ['.$method.'] is not found.';
                break;
        }
        return $msgError;
    }

    /**
     * Validate the min rule
     *
     * @param mixed $value
     * @param mixed $min
     * @return bool
     */
    public function min($value, $min = 0)
    {
        if (!is_numeric($min) || !is_int((int)$min)) {
            return false;
        }

        // for string or number
        if (is_string($value) || is_numeric($value)) {
            return strlen($value) >= (int)$min;
        }
        // for array
        if (is_array($value)) {
            return count($value) >= (int)$min;
        }
        // for file (size in kilobytes)
        return is_file($value) && ((filesize($value) / 1024) >= (int)$min);
    }

    /**
     * Validate the max rule
     *
     * @param mixed $value
     * @param mixed $min
     * @return bool
     */
    public function max($value, $max = 0)
    {
        if (!is_numeric($max) || !is_int((int)$max)) {
            return false;
        }
        // for string or number
        if (is_string($value) || is_numeric($value)) {
            return strlen($value) <= (int)$max;
        }
        // for array
        if (is_array($value)) {
            return count($value) <= (int)$max;
        }
        // for file (size in kilobytes)
        return is_file($value) && ((filesize($value) / 1024) <= (int)$max);
    }

    /**
     * Validate the size rule
     *
     * @param mixed $value
     * @param mixed $min
     * @return bool
     */
    public function size($value, $size = 0)
    {
        if (!is_numeric($size) || !is_int((int)$size)) {
            return false;
        }
        // for string or number
        if (is_string($value) || is_numeric($value)) {
            return strlen($value) === (int)$size;
        }
        // for array
        if (is_array($value)) {
            return count($value) === (int)$size;
        }
        // for file (size in kilobytes)
        return is_file($value) && ((filesize($value) / 1024) === (int)$size);
    }
}
