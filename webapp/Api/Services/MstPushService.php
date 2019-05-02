<?php
/**
 * @author  Huynh Phat <phat.nguyen@persol.co.jp>
 * @license [v1]
 */
namespace App\Api\Services;

use App\Api\Traits\FactoryTrait;

class MstPushService
{
    use FactoryTrait;

    /**
     * @var MstPush
     */
    private $mstPush;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->mstPush = $this->model('api/M_push');
    }

    /**
     * Create a new push notification
     *
     * @param array $params
     * @param array $headers
     *
     * @return bool|integer
     */
    public function createPush($params = [], $headers = [])
    {
        if (empty($params)) {
            return false;
        }
        // insert many
        if (isset($params['data'])) {
            return $this->mstPush->createPushMany($params['data'], $headers);
        }
        // insert one
        return $this->mstPush->createPush($params, $headers);
    }
}
