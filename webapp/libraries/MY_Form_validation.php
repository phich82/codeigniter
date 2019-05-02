<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Define the extra rules for Form Validation
 * (Extending Native Libraries: the class name must start with MY_ as in config/config.php)
 */
class MY_Form_validation extends CI_Form_validation
{
    protected $CI;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // reference to the CodeIgniter super object
        $this->CI =& get_instance();
    }

    /**
     * Validate input as an email
     *
     * @param string $email [email]
     *
     * @return boolean
     */
    public function email($email)
    {
        // define message for this rule here or set message in lang file (form_validation_lang.php)
        // $this->CI->form_validation->set_message('email', 'The %s is not valid.');
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate input as a string
     *
     * @param string $input [input]
     *
     * @return boolean
     */
    public function string($input)
    {
        return is_string($input);
    }

    /**
     * Validate the input as an array
     *
     * @param string $input [input]
     *
     * @return boolean
     */
    public function array2($input, $field = null)
    {
        $params = !empty($this->validation_data) ? $this->validation_data : array_merge($this->CI->input->post(), $this->CI->input->get());

        if (empty($field)) {
            throw new Exception('The array rule is required an argument.');
        }

        $split = explode(':', $field);
        $field = str_replace(['[', ']'], ['.', ''], array_shift($split));
        $parts = explode('.', $field);

        // get the sub rules if any
        $subrules = [];
        if (count($split) > 0) {
            // get the sub rules if any
            $subrules = $this->_extractSubRules($split);
        }

        // in case the field is not nested
        if (count($parts) === 1) {
            if (empty($subrules)) {
                return array_key_exists($field, $params) && is_array($params[$field]);

            }//var_dump($field, $params[$field], $subrules);exit;
            return $this->_validateSubrulesForArray($field, $params[$field], $subrules);
        }

        // get the sub rules if any
        //$subrules = $this->_extractSubRules($split);

        $firstElement = array_shift($parts);
        $valuesCheck  = [$firstElement => $params[$firstElement]];

        // in case, inputs are checkboxes or radios
        if (!array_key_exists($firstElement, $params)) {
            return false;
        }

        $currentValue = $params[$firstElement];

        foreach ($parts as $k => $part) {
            if ($part == '*') {
                $totalElements = count($currentValue);
                $currentValue  = $currentValue[array_keys($currentValue)[0]];
                $temp = [];
                for ($s=0; $s < $totalElements; $s++) {
                    foreach ($valuesCheck as $rule => $value) {
                        $temp[$rule."[".$s."]"] = $value[$s];
                        // if it is the last element, check whether it is an array & check the sub rules of it (min, max, size) if any
                        if (count($parts) === ($k + 1) && !$this->_validateSubrulesForArray($rule.'['.$s.']', $value[$s], $subrules)) {
                            return false;
                        }
                    }
                }
                $valuesCheck = $temp;
            } else {
                $currentValue = $currentValue[$part];
                $temp = [];
                foreach ($valuesCheck as $rule => $value) {
                    $temp[$rule."['".$part."']"] = $value[$part];
                    // if it is the last element, check whether it is an array & check the sub rules of it (min, max, size) if any
                    if (count($parts) === ($k + 1) && !$this->_validateSubrulesForArray($rule.'['.$part.']', $value[$part], $subrules)) {
                        return false;
                    }
                }
                $valuesCheck = $temp;
            }
        }

        return true;
    }

    /**
     * Validate the sub rules of array (min, max, size) if any
     *
     * @param string $field
     * @param mixed $valueChecked
     * @param array $subRules
     * @return bool
     */
    private function _validateSubrulesForArray($field, $valueChecked, $subRules = [])
    {
        // the checked value is not an array
        if (!is_array($valueChecked)) {
            $this->set_rules($field, '', 'array', ['array' => 'It must be an array.']);
            return false;
        }

        // check the sub rules of array (min, max, size) if any
        if (!empty($subRules)) {
            foreach ($subRules as $subRule) {
                if (method_exists($this, $subRule['method'])) {
                    switch ($subRule['method']) {
                        case 'min':
                            $msgError = 'It must have at least '.$subRule['size'].'.';
                            break;
                        case 'max':
                            $msgError = 'It must have at maxium '.$subRule['size'].'.';
                            break;
                        case 'size':
                            $msgError = 'It must have the size as '.$subRule['size'].'.';
                            break;
                        default:
                            $msgError = 'Rule '.$subRule['method'].' not found.';
                            break;

                    }
                    $this->set_rules($field, '', 'array', ['array' => $msgError]);
                    if (!$this->{$subRule['method']}($valueChecked, $subRule['size'])) {
                        return false;
                    }
                }
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
    private function _extractSubRules($rulesArray)
    {
        $subrules = [];
        if (!empty($rulesArray)) {
            foreach ($rulesArray as $rule) {
                $pattern = '#^(?P<method>\w+)\[(?P<size>\d+)\]$#';
                if (preg_match($pattern, $rule, $matches) === 1) {
                    $subrules[] = [
                        'method' => $matches['method'],
                        'size'   => (int)$matches['size']
                    ];
                }
            }
        }
        return $subrules;
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

    /**
     * Validate the input is in a given array
     *
     * @param string $input []
     *
     * @return boolean
     */
    public function in($input, $in = null)
    {
        if (!is_string($in)) {
            return false;
        }
        return in_array($input, explode(',', $in));
    }

    /**
     * Validate the input is not in an array
     *
     * @param string $input [input]
     * @param array  $in    [array of the given values]
     *
     * @return boolean
     */
    public function not_in($input, $in = null)
    {
        if (!is_string($in)) {
            return false;
        }
        return !in_array($input, explode(',', $in));
    }

    /**
     * Trim the value
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function trim($value)
    {
        return trim($value);
    }

    /**
     * Accept the null value
     *
     * @param mixed $value []
     *
     * @return bool
     */
    public function nullable($value)
    {
        return true;
    }

    public function bool($value, $typeInput = null)
    {
        switch (strtolower($typeInput)) {
            case 'checkbox':
            case 'radio':
                return is_null($value) || $value === 'on';
            default:
                return is_bool($value);
        }
    }

    public function datetime($date, $format = 'Y-m-d H:i:s') {
        if (date($format, strtotime($date)) == $date) {
            return true;
        }
        $this->set_message('datetime', 'The {field} field must have format [' . $format . ']');
        return FALSE;
    }

    /**
     * Validate the rule for checking the record exists in database by the given parameters
     *
     * @param  mixed $value
     * @param  string $params [exist_by[orders:company_code:store_no]]
     *
     * @return bool
     */
    public function exist_by($value, $params)
    {
        if (empty($params)) {
            $this->set_message('exist_by', 'The parameters [table, fields] for the rule [exist_by] are missed.');
            return false;
        }

        $params = explode(':', $params);
        $table  = array_shift($params);

        if (!empty($params)) {
            $dataValidation = !empty($this->validation_data) ? $this->validation_data : array_merge(
                $this->CI->input->post(),
                $this->CI->input->get()
            );

            $where = [];

            // for this field
            $first = explode('=>', array_shift($params));
            // mapping to column if any
            $where[count($first) === 2 ? $first[1] : $first[0]] = $value;

            // for other fields if any
            foreach ($params as $param) {
                $split = explode('=>', $param);
                $field = $split[0];
                // field not found
                if (!isset($dataValidation[$field])) {
                    $this->set_message('exist_by', 'The field ['.$field.'] for the rule [exist_by] does not found.');
                    return false;
                }
                // mapping to column if any
                $where[count($split) === 2 ? $split[1] : $field] = $dataValidation[$field];
            }

            $this->CI->load->database();
            $rows = $this->CI->db->from($table)->where($where)->count_all_results();

            // not found (no exist)
            if ($rows <= 0) {
                $this->set_message('exist_by', 'This record ['.$value.'] does not found.');
                return false;
            }

            return true;
        }

        // missing the where conditions
        $this->set_message('exist_by', 'The parameters [fields] for the rule [exist_by] are missed.');
        return false;
    }

    public function test($value, $params)
    {
        if (empty($params)) {
            $this->CI->form_validation->set_message('test', 'The parameters of the rule [test] are missed.');
            return false;
        }

        $fields = explode(':', $params);

        if (!empty($fields)) {
            $dataValidation = !empty($this->validation_data) ? $this->validation_data : array_merge(
                $this->CI->input->post(),
                $this->CI->input->get()
            );
            foreach ($fields as $field) {
                $split = explode('=>', $field);
                $fieldThis = $split[0];
                // not the nested field but the field not found
                if (strpos($fieldThis, '.') === false && !isset($dataValidation[$fieldThis])) {
                    $this->CI->form_validation->set_message('test', 'The field ['.$fieldThis.'] for the rule [exist_by] does not found.');
                    return false;
                }
                // the nested field but the values are different
                if (strpos($fieldThis, '.') !== false) {
                    $valueChecked = &$this->array_access($dataValidation, explode('.', $fieldThis));
                    if ($value != $valueChecked) {
                        $this->CI->form_validation->set_message('test', 'The values ['.$value.', '.$valueChecked.'] are not same.');
                        return false;
                    }
                }
            }
            return true;
        }
        // missing the where conditions
        $this->CI->form_validation->set_message('test', 'The parameters of the rule [test] are missed.');
        return false;
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
                throw new Exception("Field [$key] is invalid.");
            }
            // get and return the reference to the sub-array with the current key
            $subarray =& $this->array_access($array[$key], $keys);
            return $subarray;
        }
        // return the match
        return $array;
    }




    /**
     * Accept the in rule
     *
     * @param string $value
     * @param string $list
     * @return bool
     */
    public function in($value, $list)
    {
        if (!in_array($value, explode(',', $list))) {
            $this->set_message('in', 'The {field} field must be one of [' . $list . ']');
            return false;
        }
        return true;
    }

	/**
	 * datetime
	 *
	 * @param string $date
	 * @param string $format
	 * @return bool
	 */
	public function datetime($date, $format = 'Y-m-d H:i:s') {
        if (date($format, strtotime($date)) == $date) {
            return true;
        }
        $this->set_message('datetime', 'The {field} field has the wrong value or not in the format [' . $format . ']');
        return false;
	}

	/**
     * Validate the rule for checking the record exists in database by the given parameters
     *
     * @param  mixed $value
     * @param  string $params [exist_by[orders:company_code:store_no]]
     *
     * @return bool
     */
    public function exist_by($value, $params)
    {
        if (empty($params)) {
            $this->set_message('exist_by', 'The parameters [table, fields] in the rule [exist_by] are missed.');
            return false;
        }
        $params = explode(':', $params);
        $table  = array_shift($params);
        if (!empty($params)) {
			$CI =& get_instance();
            $dataValidation = !empty($this->validation_data) ? $this->validation_data : array_merge(
                $CI->input->post(),
                $CI->input->get()
            );
            $where = [];
            // for this field
            $first = explode('=>', array_shift($params));
            // mapping to column if any
            $where[count($first) === 2 ? $first[1] : $first[0]] = $value;
            // for other fields if any
            foreach ($params as $param) {
                $split = explode('=>', $param);
                $field = $split[0];
                // field not found
                if (!isset($dataValidation[$field])) {
                    $this->set_message('exist_by', 'The field ['.$field.'] in the rule [exist_by] does not found.');
                    return false;
                }
                // mapping to column if any
                $where[count($split) === 2 ? $split[1] : $field] = $dataValidation[$field];
            }
            $CI->load->database();
            $rows = $CI->db->from($table)->where($where)->count_all_results();
            // not found (no exist)
            if ($rows <= 0) {
                $this->set_message('exist_by', '['.$value.'] not found.');
                return false;
            }
            return true;
        }
        // missing the where conditions
        $this->set_message('exist_by', 'The parameters [fields] for the rule [exist_by] are missed.');
        return false;
	}

	/**
	 * Set the 'required' rule by the another fields
	 *
	 * @param  mixed $value
	 * @param  string $params
	 *
	 * @return bool
	 */
	public function required_by($value, $params)
    {
        if (empty($params)) {
            $this->set_message('required_by', 'The parameters of the rule [required_by] are missed.');
            return false;
        }
		$fields = explode(':', $params);

        if (!empty($fields)) {
			$CI =& get_instance();
            $dataValidation = !empty($this->validation_data) ? $this->validation_data : array_merge(
                $CI->input->post(),
                $CI->input->get()
            );
            foreach ($fields as $field) {
				$split = explode('=>', $field);
				// missing the values for checking
				if (count($split) !== 2) {
					$this->set_message('required_by', 'Missing the given values for checking the value of the field ['.$fieldThis.'] => [required_by rule].');
                    return false;
				}
				$fieldThis   = $split[0];
				$valuesGiven = explode(',', $split[1]);
                // not the nested field but the field not found
                if (strpos($fieldThis, '.') === false && !isset($dataValidation[$fieldThis])) {
                    $this->set_message('required_by', 'The field ['.$fieldThis.'] for the rule [required_by] not found.');
                    return false;
                }
                // the nested field but the values are not in the given list
                if (strpos($fieldThis, '.') !== false) {
					$valueChecked = &$this->array_access($dataValidation, explode('.', $fieldThis));
					// required when in the given list but the value is empty
                    if (in_array((string)$valueChecked, $valuesGiven) && empty($value)) {
                        $this->set_message('required_by', 'The {field} field is required.');
                        return false;
                    }
                }
            }
            return true;
        }
        // missing the where conditions
        $this->set_message('required_by', 'The parameters of the rule [required_by] are missed.');
        return false;
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
                throw new Exception("Field [$key] is invalid.");
            }
            // get and return the reference to the sub-array with the current key
            $subarray =& $this->array_access($array[$key], $keys);
            return $subarray;
        }
        // return the match
        return $array;
    }
}
