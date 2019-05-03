<?php
/**
* @author Huynh Phat <phat.nguyen@persol.co.jp>
* @license [v1]
*/
namespace App\Api\Services;

use App\Api\Traits\FactoryTrait;
use App\Api\Helpers\ApiLog;

require_once APPPATH.'Api/Traits/FactoryTrait.php';
require_once APPPATH.'Api/Helpers/ApiLog.php';

/**
 * Create the resource from the restful APIs (providers)
 */
class AwsConnectCloudService
{
    use FactoryTrait;

    /**
     * @var Aws_connect_cloud
     */
    private $apiAwsConnectCloud;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiAwsConnectCloud = $this->library('aws_connect_cloud');
        $this->trnPushNotificationService = $this->library('api/T_push_notification_service');
    }

    /**
     * Send the updated orders to Aws Connect Cloud
     * (RSV Cloud will call it)
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return void
     */
    public function pushNotification($params = [], $headers = [])
    {
        $ordersPushed  = [];
        $errorsMapping = [];
        $data = [];

        $stores = $this->trnPushNotificationService->getStores(['order_ids' => $params['push_order_ids']], $headers);

        foreach ($params['data'] as $param) {
            $pos_company = $param['pos_company_code'] ?? $headers['company_code'];
            $pos_store   = $param['pos_store_code']   ?? $headers['store_code'];

            // check whether the store has already been mapped?
            if (empty($store = $this->_checkStore($stores, 'order_id', $param['order_id']))) {
                $errorsMapping[] = ['pos_company_code' => $pos_company, 'pos_store_code' => $pos_store, 'order_id' => $param['order_id']];
                continue;
            }

            // check the order whether pushed to Connect Cloud
            if ($store['notify_status'] == CONNECT_CLOUD_RESPONSE_SUCCESS) {
                $ordersPushed[] = $param['order_id'];
                continue;
            }

            $request = $this->_getRequestFormat($store);

            // TODO: we will replace 'mockPost' with 'post' when POS+ Connect provides it
            $response = $this->apiAwsConnectCloud->mockPost('/regist_notification', $request);

            $contents = [
                'request'  => $request,
                'headers'  => $response['headers'],
                'response' => $response['response'],
            ];
            $time = date('YmdHis');
            $data[] = [
                'pos_company_code' => $pos_company,
                'pos_store_code'   => $pos_store,
                'order_id'         => $param['order_id'],
                'message_no'       => 1,
                'contents'         => json_encode($contents, JSON_UNESCAPED_UNICODE),
                'sent_status'      => $response['status_code'] < 400 ? CONNECT_CLOUD_RESPONSE_SUCCESS : CONNECT_CLOUD_RESPONSE_FAILED,
                'sent_date'        => $request['send_date'],
                'create_time'      => $time,
                'create_user'      => DB_USER_VALUE,
                'update_time'      => $time,
                'update_user'      => DB_USER_VALUE,
                'delete_flg'       => UNDELETE_FLAG_VALUE,
            ];
        }

        if (!empty($data)) {
            $columnsNotUpdated = [
                'order_id',
                'pos_company_code',
                'pos_store_code',
                'create_time',
                'create_user',
                'delete_flg'
            ];
            $this->trnPushNotificationService->updateOrInsertMany($data, $headers, $columnsNotUpdated, false);
        }

        // track errors of mapping
        if (!empty($errorsMapping)) {
            ApiLog::info('Mapping errors of POS+ and Store : '.json_encode($errorsMapping));
        }

        // track the orders existsed (pushed)
        if (!empty($ordersPushed)) {
            ApiLog::info('The orders could not push to POS+ Connect because they pushed: '.json_encode($ordersPushed));
        }
    }

    /**
     * Push notification by async
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return bool
     */
    public function pushNotificationAsync($params = [], $headers = [])
    {
        return $this->trnPushNotificationService->updateOrInsertMany($params, $headers);
    }

    /**
     * Push notification by cronjob
     *
     * @param  integer $limit
     *
     * @return mixed
     */
    public function pushNotificationCron($limit = 100)
    {
        if (empty($dataPush = $this->trnPushNotificationService->getPushNotifications($limit))) {
            return null;
        }
        foreach ($dataPush as $data) {
            $this->pushNotification($data, $data['headers']);
        }
    }

    /**
     * Prepare the request data for sending POS+ Connect
     *
     * @param  array $params
     *
     * @return array
     */
    private function _getRequestFormat($params = [])
    {
        return [
            // 01: Web reservation, 02: POSlab, 03: staff, 04: Reservation
            'sub_system_code' => $params['sub_system_code'] ?? '04',   //4: reservation
            'notification_type' => $params['notification_type'] ?? 5, //5: Messages
            // 0: Send all customers, 1: Specify destination as card_numbers (default: 0)
            'send_target_flg' => $params['send_target_flg'] ?? 1,
            // ['xxxx', 'zzzz', ...] format
            //'card_number' => $params['card_number'] ?? [],
            'customer_id' => $params['customer_code'] ??  [],
            // send_date = reserve_date + reserve_time(in table [trn_self_orders]) - 15minutes
            'send_date' => $this->_calculateSendDate($params),
            // BASE 64 code of image => no plan
            'image' => $params['image'] ?? '',
            // jpg or png => no plan
            'image_type' => $params['image_type'] ?? '',
            'title' => $this->_message('title', $params),
            'description' => $this->_message('description', $params),
            // pos_push_id: xxxx, pos_target_id: zzzz
            'relation_key' => $params['relation_key'] ?? '',
            'transition_value' => $params['transition_value'] ?? '',
        ];
    }

    /**
     * Re-calculate the reserve date
     *
     * @param  array $params
     * @return string
     */
    private function _calculateSendDate($params = [])
    {
        $sendDate = '';
        if (isset($params['reserve_date']) && !empty($params['reserve_date'])) {
            $timeBeforeSend = $params['time_before_send'] ?: BEFORE_RECEIVING_TIME;
            $seconds = strtotime('-'.$timeBeforeSend.' minutes', strtotime($params['reserve_date']));
            $sendDate = date('Y-m-d H:i:s', $seconds);
        }
        return $sendDate;
    }

    /**
     * Translate the message by the specified language
     *
     * @param  string $key
     * @param  string $lang
     *
     * @return string
     */
    private function _trans($key, $lang = null)
    {
        $lang = $lang ?: DEFAULT_STORE_LANG_CODE;

        $messages = [
            'eng' => [
                'title'       => PUSH_NOTIFICATION_TITLE_EN,
                'description' => PUSH_NOTIFICATION_DESC_EN,

            ],
            'jpn' => [
                'title'       => PUSH_NOTIFICATION_TITLE_JP,
                'description' => PUSH_NOTIFICATION_DESC_JP,
            ]
        ];

        return isset($messages[$lang]) ? $messages[$lang][$key] : $messages[DEFAULT_STORE_LANG_CODE][$key];
    }

    /**
     * Get message (title or description) by the language code
     *
     * @param  string $type [title or description]
     * @param  array  $params
     *
     * @return string
     */
    private function _message($type, $params = [])
    {
        $lang = $params['lang_code'];
        if (empty($params[$type])) {
            return $this->_trans($type, $lang);
        }
        $messages = unserialize($params[$type]);
        return $messages[$lang] ?? $this->_trans($type, $lang);
    }

    /**
     * Filter order with the given parameters
     *
     * @param  array $stores
     * @param  string $keyCheck
     * @param  mixed $valueCheck
     *
     * @return array|null
     */
    private function _checkStore(&$stores, $keyCheck, $valueCheck)
    {
        if (empty($stores)) {
            return null;
        }
        foreach ($stores as $k => $store) {
            if (isset($store[$keyCheck]) && $store[$keyCheck] == $valueCheck) {
                $out = $store;
                unset($stores[$k]);
                return $out;
            }
        }
        return null;
    }
}
