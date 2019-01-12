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
        $di = load_class('DI', 'Api/Ioc');
        echo json_encode($di->test());
        //$result = $this->apiRsvSalesService->getGuzzle('/posts');
        //echo json_encode($result);
    }

    public function posts($id = null)
    {
        
        //echo json_encode(['id' => $id]);
    }
}
