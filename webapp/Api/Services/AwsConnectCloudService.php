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
        $this->apiAwsConnectCloud  = $this->library('aws_connect_cloud');
        $this->trnPushNotificationService = $this->make('api/T_push_notification_service', null, 'trnPushNotificationService');
    }

    /**
     * Send the updated orders to Aws Connect Cloud
     * (RSV Cloud will call it)
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return mixed
     */
    public function pushNotification($params = [], $headers = [])
    {
        $ordersPushed = [];
        foreach ($params as $k => $param) {
            $conditions = [
                'pos_company_code' => $param['company_code'] ?? $headers['company_code'],
                'pos_store_code'   => $param['store_code']   ?? $headers['store_code'],
                'order_id'         => $param['order_id'],
                'sent_status'      => CONNECT_CLOUD_RESPONSE_SUCCESS
            ];

            // check the order whether pushed to Connect Cloud
            if ($this->trnPushNotificationService->isExistedBy($conditions)) {
                $ordersPushed[] = $param['order_id'];
                continue;
            }

            //TODO need to move it out side the loop , already send
            $store = $this->trnPushNotificationService->getStoreDetail($param, $headers);

            // create a new push notification for storage DB
            $result = $this->trnPushNotificationService->create(array_merge([
                'company_code' => $conditions['pos_company_code'],
                'store_code'   => $conditions['pos_store_code'],
                'send_date'    => $this->_calculateSendDate($store),
                'order_id'     => $param['order_id'],
            ], $param), $headers);

            $request  = $this->_getRequestFormat($store);

            // TODO: Will removed after have uri
            $response = $this->apiAwsConnectCloud->mockPost('/regist_notification', $request);
            //$response = $this->apiAwsConnectCloud->post('/regist_notification', $request);

            $result['contents']['request']  = $request;
            $result['contents']['headers']  = $response['headers'];
            $result['contents']['response'] = $response['response'];
            $result['contents'] = json_encode($result['contents'], JSON_UNESCAPED_UNICODE);

            $this->trnPushNotificationService->updateBy($result, $headers);
        }

        // track the orders existsed (pushed)
        if (!empty($ordersPushed)) {
            ApiLog::info('The orders failed because they pushed to connect cloud : '.json_encode($ordersPushed));
        }
    }

    /**
     *
     * @param  array $params
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
            'title' => $params['title'] ?: $this->_trans('title', $params['lang_code']),
            'description' => $params['description'] ?: $this->_trans('description', $params['lang_code']),
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

}
