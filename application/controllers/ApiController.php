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

        $params = [
            //'a' => 1,
            'id' => 1,
            'name' => 'jhp'
        ];
        var_dump($this->fieldsTableExists(array_keys($params), 'messages'));
        //$di = load_class('DI', 'Api/Ioc');
        //echo json_encode($di->test());
        //$result = $this->apiRsvSalesService->getGuzzle('/posts');
        //echo json_encode($result);
    }

    public function posts()
    {
        $result = $this->apiRsvSalesService->posts([]);
        echo json_encode(['result' => $result]);
    }

    public function all()
    {
        $result = $this->apiRsvSalesService->all([]);
        echo json_encode(['result' => $result]);
    }

    /**
     * Check the specified fields whether they do exist in database
     *
     * @param array|string $fieldsChecked
     * @param string $table
     *
     * @return bool
     */
    private function fieldsTableExists($fieldsChecked = [], $table = null)
    {
        $CI = &get_instance();
        $CI->load->database();

        $fieldsTableExists = function ($fieldsChecked = [], $table = null) use ($CI) {
            if (is_array($fieldsChecked) && count($fieldsChecked) === 0) {
                return false;
            }
            $fieldsChecked = is_string($fieldsChecked) ? [$fieldsChecked] : $fieldsChecked;
            return empty(array_diff($fieldsChecked, $CI->db->list_fields($table)));
        };
        return $fieldsTableExists($fieldsChecked, $table);
    }
}
