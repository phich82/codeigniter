<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'Validator.php';

/**
 * Validate the form from the incomming request.
 */
class FormRequest extends Validator
{
    public $error;
    public $params;
    protected $request;
    protected $input;
    private $CI;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct($data = [])
    {
        // validate the parameters from the request
        $this->_validate($data);
    }

    /**
     * Check if the error occured
     *
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }

    /**
     * Get the error.
     *
     * @return array|null
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Validate the form.
     *
     * @param  array $data
     * @return void
     */
    private function _validate($data)
    {
        if ($this->authorize() === true) {
            // load the form_validation library
            $this->CI =& get_instance();
            $this->CI->load->library('form_validation');
            $this->input   = $this->CI->input;
            $this->request = $this->CI->input;

            // set the custom data if any
            if (!empty($data)) {
                $this->CI->form_validation->set_data($data);
            }

            // get all parameters from the request
            $this->params = $this->_getDataValidation();

            // set delimiter
            $this->CI->form_validation->set_error_delimiters(null, null);

            // validate form
            $this->CI->form_validation->set_rules($this->_getRules())->run();

            // track the error if any
            if (!empty($error = $this->CI->form_validation->error_array())) {
                $this->error = $error;
            }
        }
    }

    /**
     * Get the rules.
     *
     * @return array
     */
    private function _getRules()
    {
        $messages   = $this->_getMessages();
        $attributes = $this->attributes();

        $config = [];
        foreach ($this->rules() as $field => $rules) {
            if (strpos($field, '.') === false) { // not nested field
                $config[] = $this->_getRuleConfig($field, $attributes, $rules, $messages);
            } else { // the nested field
                foreach ($this->_getNestedArrayFields($field) as $nestedField) {
                    $rulesChecked = $this->_processNestedFieldsInRules($nestedField, $rules);
                    $config[] = $this->_getRuleConfig($nestedField, $attributes, $rulesChecked, $messages, true);
                }
            }
        }

        return $config;
    }

    /**
     * Process the nested fields in rules
     *
     * @param  string $nestedFieldKey
     * @param  string $rulesValue
     *
     * @return string
     */
    private function _processNestedFieldsInRules($nestedFieldKey, $rulesValue)
    {
        $star  = '*';
        // the nested fields not found
        if (strpos($rulesValue, $star) === false) {
            return $rulesValue;
        }

        $dot   = '.';
        $colon = ':';
        $slash = '|';
        $arrow = '=>';
        $bracketL = '[';
        $bracketR = ']';

        // extract the keys of the nested field
        $keysRoot   = explode($dot, str_replace($bracketL, $dot, str_replace($bracketR.$bracketL, $dot, rtrim($nestedFieldKey, $bracketR))));
        $rulesSplit = explode($slash, $rulesValue);
        $out = [];

        foreach ($rulesSplit as $ruleRight) {
            $ruleNoBrackets = preg_replace('/(.+)\[(.*)\]$/i', '$1'.$colon.'$2', $ruleRight);
            $parts = explode($colon, $ruleNoBrackets);
            $ruleName = array_shift($parts);

            // build a new rule string
            $rule = $ruleName.$bracketL;
            // loop the nested keys
            foreach ($parts as $part) {
                // check the mapping key if any
                $split = explode($arrow, $part);
                // extract the parts of this key
                $keys  = explode($dot, $split[0]);
                // check whether it is the nested key
                if (count($keysRoot) === count($keys)) {
                    foreach ($keysRoot as $k => $v) {
                        // replace '*' with the numerical value matches to the nested position of key in the nested root field
                        if (is_numeric($v) && $keys[$k] == $star) {
                            $keys[$k] = $v;
                        }
                    }
                    // append to the rule string
                    $rule .= implode($dot, $keys).(count($split) === 2 ? $arrow.$split[1] : '').$colon;
                } else {
                    // append to the rule string
                    $rule .= $part.$colon;
                }
            }
            $out[] = rtrim($rule, $colon).$bracketR;
        }
        return implode($slash, $out);
    }

    /**
     * Get the rule config for every field
     *
     * @param  string       $field
     * @param  string|array $attributes
     * @param  string       $rules
     * @param  string|array $messages
     * @param  bool         $isNested
     * @return array
     */
    private function _getRuleConfig($field, $attributes, $rules, $messages, $isNested = false)
    {
        $label  = is_array($attributes) ? ($attributes[$field] ?? null) : $attributes;
        $errors = is_array($messages)   ? ($messages[$field]   ?? null) : $messages;

        $configRule = [
            'field'  => $field,
            'label'  => $label,
            'rules'  => $rules,
            'errors' => $errors,
        ];

        // check the rule of array and the subrules of array if any
        $arrayRule = $this->_getArrayRule($field, $rules, $isNested);
        if (!empty($arrayRule)) {
            $configRule['rules'] = [$arrayRule];
        }

        return $configRule;
    }

    /**
     * Get the nested array rules
     * Example: ['colors.4.color.*' => 'array']
     *
     * @param  string $field
     * @return array
     */
    private function _getNestedArrayFields($field)
    {
        $data  = $this->_getDataValidation();
        $parts = explode('.', $field);
        $firstElement = array_shift($parts);
        $rules = [$firstElement];

        // in case the inputs are checkboxes, ignore them
        if (!array_key_exists($firstElement, $data)) {
            return [];
        }

        $currentValue = $data[$firstElement];

        foreach ($parts as $part) {
            if ($part == '*') {
                // key not exist, ignore it
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
                // key not exist, ignore it
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
     * Throw exception
     *
     * @param  string $field
     * @return void
     * @throws Exception
     */
    private function _throwError($field)
    {
        throw new Exception("Field [$field] is invalid.");
    }

    /**
     * Set the rule of array and the subrules of array
     *
     * @param  string $field
     * @param  array  $rules
     * @return array|null
     */
    private function _getArrayRule($field, $rules, $isNested = false)
    {
        $arrayRuleName   = 'array';
        $hasNullableRule = false;

        // check the rule of array if found
        $arrayRule = array_filter(explode('|', $rules), function ($item) use ($arrayRuleName, &$hasNullableRule) {
            $hasNullableRule = $item == 'nullable';
            return substr($item, 0, strlen($arrayRuleName)) == $arrayRuleName;
        });

        if (!empty($arrayRule)) {
            try {
                $valueChecked = $this->_getValueChecked($field, $isNested);
            } catch (Exception $e) { // field not exist
                $this->set_message($field, 'The {field} field is not found.');
                return ['required', function ($value) use ($hasNullableRule) {
                    return $hasNullableRule;
                }];
            }

            $subrules = $this->_extractSubRules($this->_getSubRules($arrayRule));

            // Note: CI will ignore the field that its value is an empty array.
            // So, to fix this when the subrules exist and the value is an empty array,
            // set its value to null
            if (!empty($subrules) && empty($valueChecked)) {
                $this->set_null($field);
                /* we do not need to check the error (field not exist) because it was checked above.
                if ($this->set_null($field) === false) {
                    return ['required', function ($value) use ($hasNullableRule) {
                        return $hasNullableRule;
                    }];
                }*/
            }

            return [$arrayRuleName, function ($value) use ($valueChecked, $subrules, $arrayRuleName) {
                // check it is an array
                if (!is_array($valueChecked)) {
                    $this->set_message($arrayRuleName, 'The {field} field must be an array.');
                    return false;
                }
                // validate the subrules of array (min, max, size) if found
                return $this->_validateArraySubrules($valueChecked, $subrules, $arrayRuleName);
            }];
        }
        return null;
    }

    /**
     * Get the checked value from the array by key
     *
     * @param  string $field
     * @param  bool $isNested
     * @return mixed
     * @throws Exception
     */
    private function _getValueChecked($field, $isNested = false)
    {
        $data = $this->_getDataValidation();
        if ($isNested === true) {
            return $this->array_access($data, $field);
        }
        return isset($data[$field]) ? $data[$field] : $this->_throwError($field);
    }

    /**
     * Get the data for validation
     *
     * @param  array $dataCustom
     * @return array
     */
    private function _getDataValidation($dataCustom = [])
    {
        if (is_array($dataCustom) && !empty($dataCustom)) {
            return $dataCustom;
        }
        return !empty($this->CI->form_validation->validation_data)
                    ? $this->CI->form_validation->validation_data
                    : array_merge(
                        $this->CI->input->post(),
                        $this->CI->input->get()
                    );
    }

    /**
     * Get the sub rules from a string
     *
     * @param  array|string $arrayRule
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
     * @param  string $field
     * @param  mixed  $valueChecked
     * @param  array  $subRules
     * @param  string $arrayRuleName
     * @return bool
     */
    private function _validateArraySubrules($valueChecked, $subrules = [], $arrayRuleName = 'array')
    {
        // the checked value is not an array
        if (!is_array($valueChecked)) {
            $this->set_message($arrayRuleName, 'The {field} field must be an array.');
            return false;
        }

        // check the sub rules of array (min, max, size) if any
        foreach ($subrules as $subrule) {
            // the subrule method not found
            if (!method_exists($this, $subrule['method'])) {
                $this->set_message($arrayRuleName, 'The rule ['.$subrule['method'].'] for the {field} field is not found.');
                return false;
            }
            // validate the subrule of array
            if (!$this->{$subrule['method']}($valueChecked, $subrule['size'])) {
                $this->set_message($arrayRuleName, $this->_getMsgErrorBySubrule($subrule['method'], $subrule['size']));
                return false;
            }
        }
        return true;
    }

    /**
     * Extract the sub rules if any
     *
     * @param  array $rulesArray
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
     * @param int    $size
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
     * Get the value of array by the nested key string
     *
     * @param  array $array
     * @param  array|string $keys
     * @return mixed
     * @throws Exception
     */
    private function &array_access(&$array, $keys)
    {
        // Check if the keys is a string
        if (is_string($keys)) {
            $firstChar = substr($keys, 0, 1);
            if ($firstChar != '[') {
                $pos = strpos($keys, '[');
                $keys = $pos === false ? '['.$keys.']' : '["'.substr($keys, 0, $pos).'"]'.substr($keys, $pos);
            }
            // parse it to an array
            $keys  = explode('][', preg_replace('/^\[|\]$/', '', str_replace(['"', '\''], ['', ''], $keys)));
        }

        // if it is the nested array, go deeply
        if ($keys) {
            $key = array_shift($keys);
            // key not exist
            if (!isset($array[$key])) {
                $this->_throwError($key);
            }
            // get and return the reference to the sub-array with the current key
            $subarray =& $this->array_access($array[$key], $keys);
            return $subarray;
        }
        // return the match
        return $array;
    }

    /**
     * Set the message for the given rule
     *
     * @param  string $ruleName
     * @param  string $message
     * @return void
     */
    private function set_message($ruleName, $message)
    {
        return $this->CI->form_validation->set_message($ruleName, $message);
    }

    /**
     * Set the empty array value of field to null
     *
     * @param  string $field
     * @return bool
     */
    private function set_null($field)
    {
        try {
            // the custom data
            if (!empty($this->CI->form_validation->validation_data)) {
                $vChecked = &$this->array_access($this->CI->form_validation->validation_data, $field);
                // set the array value to null
                $vChecked = null;
                return true;
            }
            // the post request method
            if (!empty($this->CI->input->post())) {
                $vChecked = &$this->array_access($this->CI->input->post(), $field);
                // set the array value to null
                $vChecked = null;
                return true;
            }
            // the get request method
            if (!empty($this->CI->input->get())) {
                $vChecked = &$this->array_access($this->CI->input->get(), $field);
                // set the array value to null
                $vChecked = null;
                return true;
            }
            return true;
        }
        // field not exist
        catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validate the nullable rule.
     *
     * @param  string|null $value
     * @return bool
     */
    private function nullable($value)
    {
        return is_null($value) || $value === '';
    }

    /**
     * Validate the min rule
     *
     * @param  mixed $value
     * @param  mixed $min
     * @return bool
     */
    private function min($value, $min = 0)
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
     * @param  mixed $value
     * @param  mixed $min
     * @return bool
     */
    private function max($value, $max = 0)
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
     * @param  mixed $value
     * @param  mixed $min
     * @return bool
     */
    private function size($value, $size = 0)
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
