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
        new TestValidationRequest();

        $this->lang->load('validation_lang');

        return $this->load->view('validation/index');
    }
}
