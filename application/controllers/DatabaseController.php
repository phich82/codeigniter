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
        $params = [
            'date < ' => date('Y-m-d'),
            'id < ' => 10
        ];
        var_dump(
            $this->message->deleteMessages([19, 100])//,
            //$this->message->find('1sdfsd'),
            //$this->message->findBy($params)
        );exit;
        $this->load->library('form_validation');

        return $this->load->view('database/index');
    }
}
