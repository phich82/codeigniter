<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'requests/TestValidationRequest.php';

class ValidationController extends CI_Controller
{
    /**
     * Index Page for this controller.
     *
     * @return object|string
     */
    public function index()
    {
        // validate params from request
        $validator = new TestValidationRequest();
        //var_dump($validator->hasError());

        $this->lang->load('form_validation_lang');

        return $this->load->view('validation/index');
    }

    public function indexPost()
    {
        // validate params from request
        $validator = new TestValidationRequest();
        //var_dump($validator->hasError());

        $this->lang->load('form_validation_lang');

        return $this->load->view('validation/index');
    }

    /**
     * @override
     * _remap
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function _remap($method, $args = []) {
        // remove suffix of method if any
        $methodNoSuffix = preg_replace('/(.*)(post|get|put|delete|head|options)/i', '$1', $method);
        // append the new suffix to it
        if (strlen($methodNoSuffix) !== strlen($method)) {
            $methodNoSuffix = $method.ucfirst($this->input->method());
        }
        if (method_exists($this, $method)) {
            // validate security here

            return call_user_func_array([$this, $method], $args);
        }
        throw new Exception('Not Method Allowed');
    }
}
