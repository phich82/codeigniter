<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'requests/ApiValidationRequest.php';
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
        $CI = &get_instance();
        $CI->load->library('form_validation');

        $data = [
            // 'roles' => [
            //     ['name' => 'name1'],
            //     //['name' => 'name2'],
            // ]
        ];
        $CI->form_validation->set_data($data);
        // validate params from request
        $validator = new ApiValidationRequest();
        var_dump($CI->form_validation->validation_data, $validator->hasError(), $validator->error());

        // $params = [
        //     //'a' => 1,
        //     'id' => 1,
        //     'name' => 'jhp'
        // ];
        // var_dump($this->fieldsTableExists(array_keys($params), 'messages'));

        // update multiple rows at once time by conditions
        //id=4: shad.stracke, message: Queen said--' 'Get to your little boy, And beat.
        // $params = [
        //     ['name' => 'cremin.nat', 'message' => "[xxx1] So she tucked her arm affectionately into."],
        //     ['name' => 'bahringer.elsa', 'message' => "[xxx1] Crab took the opportunity of adding, You're."],
        //     ['name' => 'kreiger.annabell', 'message' => "[xxx1] Rabbit actually TOOK A WATCH OUT OF ITS."],
        // ];
        // $conditions = [
        //     'status' => 1,
        //     'store' => 2
        // ];
        // $index = 'name';
        // var_dump($this->updateManyBy($params, $conditions, $index));

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

    public function updateManyBy($params = [], $conditions = [], $index = null)
    {
        $CI = &get_instance();
        $CI->load->database();

        // invalid request
        if (empty($params) || empty($conditions)) {
            return false;
        }

        // set the where conditions
        foreach ($conditions as $column => $value) {
            $CI->db->where($column, $value);
        }

        return $CI->db->update_batch('messages', $params, $index);
    }
}
