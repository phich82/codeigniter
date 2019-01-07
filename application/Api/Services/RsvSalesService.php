<?php
/**
 * @author Huynh Phat <phat.nguyen@gmail.com>
 * @license http://localhost:8282/api/v1/android [v1]
 */
namespace App\Api\Services;

class RsvSalesService
{
    /**
     * @var Rsv_sale
     */
    private $apiRsvSale;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->library('rsv_sale');
        $this->apiRsvSale = $CI->rsv_sale;
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
        return $this->apiRsvSale->get($path);
    }
}
