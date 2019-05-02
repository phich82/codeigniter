<?php
/**
* @author Huynh Phat <phat.nguyen@persol.co.jp>
* @license [v1]
*/
namespace App\Api\Services;

use App\Api\Traits\FactoryTrait;

class TrnPushNotificationService
{
    use FactoryTrait;

    /**
     * @var TrnPushNotification
     */
    private $trnPushNotification;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->trnPushNotification = $this->model('api/T_push_notification', 'trnPushNotification');
    }

    /**
     * Create a new push notification
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return array
     */
    public function create($params = [], $headers = [])
    {
        $company = $params['company_code'] ?? $headers['company_code'];
        $store   = $params['store_code'] ?? $headers['store_code'];
        $orderId = $params['order_id'];

        $conditions = [
            'pos_company_code' => $company,
            'pos_store_code'   => $store,
            'order_id'         => $orderId
        ];

        $time = date('YmdHis');
        $contents = [];

        if (isset($params['request'])) {
            $contents['request'] = $params['request'];
        }

        if (isset($params['response'])) {
            $contents['response'] = $params['response'];
        }

        if (isset($params['headers'])) {
            $contents['headers'] = $params['headers'];
        }

        $messageNo = $this->trnPushNotification->message_no($conditions);

        $data = array_merge([
            'create_time'      => $time,
            'create_user'      => DB_USER_VALUE,
            'update_time'      => $time,
            'update_user'      => DB_USER_VALUE,
            'delete_flg'       => UNDELETE_FLAG_VALUE,
            'message_no'       => $messageNo,
            'contents'         => json_encode($contents, true),
            'sent_status'      => CONNECT_CLOUD_NOT_RESPONSE,
            'sent_date'        => $params['send_date'],
        ], $conditions);

        // insert to database
        $this->trnPushNotification->createPushNotification($data);

        return array_merge([
            'message_no'       => $messageNo,
            'contents'         => $contents,
        ], $conditions);
    }

    /**
     * Get the store detail by the company code and store code
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return array
     */
    public function getStoreDetail($param = [], $headers = [])
    {
        return $this->trnPushNotification->getStoreDetail($param, $headers);
    }

    /**
     * Update one or more records at once time
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return bool
     */
    public function updateBy($params = [], $headers = [])
    {
        $conditions = [
            'message_no'       => $params['message_no'],
            'pos_company_code' => $params['pos_company_code'] ?? $headers['company_code'],
            'pos_store_code'   => $params['pos_store_code']   ?? $headers['store_code'],
            'order_id'         => $params['order_id'],
        ];
        $data = [
            'contents'    => $params['contents'],
            'sent_status' => CONNECT_CLOUD_RESPONSE_SUCCESS,
        ];
        return $this->trnPushNotification->updatePushNotificationBy($data, $conditions);
    }

    /**
     * Check the push notification whether it exists in database by conditions
     *
     * @param  array $conditions
     *
     * @return bool
     */
    public function isExistedBy($conditions = [])
    {
        return $this->trnPushNotification->isExistedBy($conditions);
    }
}
