<?php
/**
 * @author Huynh Phat <phat.nguyen@gmail.com>
 * @license http://localhost:8282/api/v1/android [v1]
 */
namespace App\Api\Services;

require_once APPPATH."Api/Traits/FactoryTrait.php";

use App\Api\Traits\FactoryTrait;

class RsvSalesService
{
    use FactoryTrait;

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
        $this->apiRsvSale = $this->make('rsv_sale');
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

    public function posts($params = [])
    {
        return $this->apiRsvSale->post('/all', $params);
    }

    public function all($params = [])
    {
        return $this->apiRsvSale->post('/posts', $params);
    }

    public function createOrders($body = [], $headers = [])
    {
        if (!empty($body)) {
            return true;
        }
        return false;
    }
}
