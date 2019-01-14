<?php

use PHPUnit\Framework\Constraint\Exception;
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
        $params = array_merge($this->CI->input->post(), $this->CI->input->get());

        if (is_null($field)) {
            throw new Exception('The array rule is required an argument.');
        }

        $split = explode(':', $field);
        $field = array_shift($split);
        $parts = explode('.', $field);

        // get the sub rules if any
        $subRules = [];
        if (!empty($split)) {
            foreach ($split as $rule) {
                $pattern = '#^(?P<method>\w+)\[(?P<size>\d+)\]$#';
                if (preg_match($pattern, $rule, $matches) === 1) {
                    $subRules[] = [
                        'method' => $matches['method'],
                        'size'   => $matches['size']
                    ];
                }
            }
        }

        // in case the field is not nested
        if (count($parts) === 1) {
            return array_key_exist($field, $params) && is_array($params[$field]);
        }
        
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
                        // if it is the last element, check whether it is an array
                        if (count($parts) === ($k + 1)) {
                            if (!is_array($value[$s])) {
                                $this->set_message($rule."[".$s."]", 'It must be an array.');
                                return false;
                            }
                            // if they have the min, max, size rules
                            if (!empty($subRules)) {
                                foreach ($subRules as $subRule) {
                                    if (method_exists($this, $subRule['method']) && !$this->{$subRule['method']}($value[$s], $subRule['size'])) {
                                        switch ($subRule['method']) {
                                            case 'min':
                                                $this->set_message($rule."[".$s."]", $rule."[".$s."]".' must have at least '.$subRule['size'].' characters.');
                                                break;
                                            case 'max':
                                                $this->set_message($rule."[".$s."]", $rule."[".$s."]".' must have at maxium '.$subRule['size'].' characters.');
                                                break;
                                            case 'size':
                                                $this->set_message($rule."[".$s."]", $rule."[".$s."]".' must have the size as '.$subRule['size'].'.');
                                                break;
                                            default:
                                                break;

                                        }
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                }
                $valuesCheck = $temp;
            } else {
                $currentValue = $currentValue[$part];
                $temp = [];
                foreach ($valuesCheck as $rule => $value) {
                    $temp[$rule."['".$part."']"] = $value[$part];
                    // if it is the last element, check whether it is an array
                    if (count($parts) === ($k + 1)) {
                        if (!is_array($value[$part])) {
                            $this->set_message($rule."[".$part."]", 'It must be an array.');
                            return false;
                        }
                        // if they have the min, max, size rules
                        if (!empty($subRules)) {
                            foreach ($subRules as $subRule) {
                                if (method_exists($this, $subRule['method']) && !$this->{$subRule['method']}($value[$part], $subRule['size'])) {
                                    switch ($subRule['method']) {
                                        case 'min':
                                            $this->set_message($rule."[".$part."]", $rule."[".$part."]".' must have at least '.$subRule['size'].' characters.');
                                            break;
                                        case 'max':
                                            $this->set_message($rule."[".$part."]", $rule."[".$part."]".' must have at maxium '.$subRule['size'].' characters.');
                                            break;
                                        case 'size':
                                            $this->set_message($rule."[".$part."]", $rule."[".$part."]".' must have the size as '.$subRule['size'].'.');
                                            break;
                                        default:
                                            break;

                                    }
                                    return false;
                                }
                            }
                        }
                    }
                }
                $valuesCheck = $temp;
            }
        }

        return true;

        // $errors = [];
        // foreach ($valuesCheck as $rule => $valueCheck) {
        //     if (!is_array($valueCheck)) {
        //         $errors[] = $rule;
        //     }
        // }
        // return count($errors) === 0;
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
        // for string or number
        if (is_string($value) || is_numeric($value)) {
            return strlen($value) >= $min;
        }
        // for array
        if (is_array($value)) {
            return count($value) >= $min;
        }
        // for file (size in kilobytes)
        return is_file($value) && ((filesize($value) / 1024) >= $min);
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
        // for string or number
        if (is_string($value) || is_numeric($value)) {
            return strlen($value) <= $max;
        }
        // for array
        if (is_array($value)) {
            return count($value) <= $max;
        }
        // for file (size in kilobytes)
        return is_file($value) && ((filesize($value) / 1024) <= $max);
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
        // for string or number
        if (is_string($value) || is_numeric($value)) {
            return strlen($value) === $size;
        }
        // for array
        if (is_array($value)) {
            return count($value) === $size;
        }
        // for file (size in kilobytes)
        return is_file($value) && ((filesize($value) / 1024) === $size);
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
}
