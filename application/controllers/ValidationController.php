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
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->lang->load('validation_lang');

        // $this->form_validation->set_rules('username', 'Username', 'required');
        // $this->form_validation->set_rules('password', 'Password', 'required');
        // $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required');
        // $this->form_validation->set_rules('email', 'Email', 'required');

        //$this->form_validation->run();
        $validation = new TestValidationRequest();
        //$validation->validate();


        return $this->load->view('validation/index');
    }
}
