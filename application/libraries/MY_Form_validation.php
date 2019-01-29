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
    public function array($input, $field = null)
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
var_dump($parts);exit('1111');
        // in case the field is not nested
        if (count($parts) === 1) {
            if (empty($subrules)) {
                return array_key_exists($field, $params) && is_array($params[$field]);

            }var_dump($field, $params[$field], $subrules);exit;
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
 var_dump($valuesCheck, $parts);exit;
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
}
