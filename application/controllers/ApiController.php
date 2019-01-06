<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApiController extends CI_Controller
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    
        $this->load->library('Api_service', null, 'apiCustomer');
    }
    
    /**
     * Index Page for this controller.
     *
     * @return object|string
     */
    public function index()
    {
        var_dump($this->apiCustomer->getGuzzle('/'));
        echo 'Api router';
    }
}
