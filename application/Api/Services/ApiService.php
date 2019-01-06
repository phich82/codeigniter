<?php
namespace application\Api\Services;

use application\Api\Api;

class ApiService
{
    /**
     * @var Api
     */
    private $api;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->library('api');
        $this->api = $CI->api;
    }
    
    /**
     * getGuzzle
     *
     * @param string $path []
     *
     * @return object
     */
    public function getGuzzle($path)
    {
        return $this->api->get($path);
    }
}
