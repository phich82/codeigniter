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
        var_dump($validator->hasError());

        $this->lang->load('form_validation_lang');

        return $this->load->view('validation/index');
    }

    public function submit()
    {
        // validate params from request
        new TestValidationRequest();
    }
}
