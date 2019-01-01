<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DatabaseController extends CI_Controller
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('message');
    }
    
    /**
     * Index Page for this controller.
     *
     * @return object|string
     */
    public function index()
    {
        var_dump($this->message->find(1));exit;
        $this->load->library('form_validation');

        return $this->load->view('database/index');
    }
}
