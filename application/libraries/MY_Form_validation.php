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
    public function is_email($email)
    {
        // define message for this rule here or set message in lang file (form_validation_lang.php)
        // $this->CI->form_validation->set_message('is_email', 'The %s is not valid.');
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
        $params = (new CI_Input())->post();
        $split = explode('.', $field);
        $flag = false;
        $check = null;
        $checks = [];
        foreach ($split as $key) {
            if ($check === null) {
                if (isset($params[$key])) {
                    $check = $params[$key];
                } else {
                    $flag = false;
                    $check = null;
                    break;
                }
            } else {
                if ($key === '*') {
                    if (empty($checks)) {
                        foreach ($check as $element) {
                            $checks[] = $element;
                        }
                    } else {
                        foreach ($checks as $element) {

                        }
                    }
                } else {

                }
                if (!isset($check[$key])) {
                    $flag = false;
                    $check = null;
                    break;
                } else {
                    $check = $check[$key];
                }
            }
        }
        $flag = is_array($check);
        var_dump($flag, $check, $params);
        return is_array($input);
    }

    /**
     * Validate the input is in an array
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
     * @param mixed $value []
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

    public function bool($value)
    {
        return is_bool($value);
    }
}
