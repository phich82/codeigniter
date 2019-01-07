<?php
/**
 * @author Huynh Phat <phat.nguyen@gmail.com>
 * @license http://localhost:8282/api/v1/android [v1]
 */
namespace App\Api\Services;

use App\Api\Traits\FactoryTrait;

require_once APPPATH.'Api/Traits/FactoryTrait.php';

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
}
