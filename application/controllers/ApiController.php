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
    
        $this->load->library('api/Rsv_sales_service', null, 'apiRsvSalesService');
    }
    
    /**
     * Index Page for this controller.
     *
     * @return object|string
     */
    public function index()
    {
        var_dump($this->apiRsvSalesService->getGuzzle('/'));
        echo 'Api router';
    }
}
