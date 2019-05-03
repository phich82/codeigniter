<?php
/**
* @author Huynh Phat <phat.nguyen@persol.co.jp>
* @license [v1]
*/
namespace App\Api\Services;

use App\Api\Traits\FactoryTrait;

/**
 * Provide the restful APIs
 */
class SelfOrdersService
{
    use FactoryTrait;

    /**
     * @var SelfOrder
     */
    private $selfOrder;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->selfOrder = $this->model('api/Self_order');
        $this->connectCloudService = $this->library('api/Aws_connect_cloud_service');
    }

    /**
     * Save the orders sent from Rsv Sales
     *
     * @param array $params
     * @param array $headers
     * @return bool
     */
    public function createOrders($params = [], $headers = [])
    {
        if (empty($params)) {
            return false;
        }
        // only update or insert an order
        if (!isset($params['multiple']) || $params['multiple'] == 'no') {
            return $this->selfOrder->updateOrInsertOrder($params, $headers);
        }
        // update the orders
        return $this->selfOrder->save($params, $headers);
    }

    /**
     * Get the orders list
     *
     * @param array $params
     * @param array $headers
     * @return array
     *
     * 1. Update status, except for cancel orders
     * 2. Send notify to POS Connect in case status change to cooking and is_notify = 1
     * 3. Return order lists (Reserved, Cancel)
     */
    public function getOrdersList($params = [], $headers = [])
    {
        $statusUpdate = true;
        if (isset($params['update_list']) && count($params['update_list']) > 0) {
            $conditions = [
                'pos_company_code' => $headers['company_code'],
                'pos_store_code'   => $headers['store_no'],
                'order_status !='  => ORDER_STATUS_CANCELLED,
            ];

            $resultUpdate = $this->_updateOrdersList($params['update_list'], $conditions);

            // push notification
            if (!empty($resultUpdate['data_push']['data'])) {
                $this->connectCloudService->pushNotificationAsync($resultUpdate['data_push']['data'], $headers);
            }
        }

        return [
            'update_status' => $statusUpdate,
            'orders' => $this->selfOrder->getOrdersList($params, $headers)
        ];
    }

    /**
     * Update the orders by conditions
     *
     * @param  array $params
     * @param  array $conditions
     * @param  string $keyIndexDb [column name in table]
     *
     * @return array
     */
    private function _updateOrdersList($params = [], $conditions = [], $keyIndexDb = 'order_id')
    {
        $data = [];
        $dataPush = [];
        $orderIdsPush = [];
        $orderIdsTrack = [];

        foreach ($params as $item) {
            // ignore the orders with the cancelled status
            if ($item['order_status'] == ORDER_STATUS_CANCELLED) {
                continue;
            }

            $dataTemp = ['order_id' => $item['order_id']];

            if (isset($item['sent_to_pos'])) {
                $dataTemp['sent_status'] = (int)$item['sent_to_pos'];
            }

            if (!empty($item['print_status'])) {
                $dataTemp['print_status'] = (int) $item['print_status'];
            }

            if (!empty($item['voucher_id'])) {
                $dataTemp['voucher'] = $item['voucher_id'];
            }

            if (!empty($item['table_number'])) {
                $dataTemp['pos_table_number'] = $item['table_number'];
            }

            if (!empty($item['order_status'])) {
                $dataTemp['order_status'] = (int)$item['order_status'];
            }

            // only filter the order with COOKING status for pushing notification
            if ($item['order_status'] == ORDER_STATUS_COOKING && $item['is_notify'] === 1) {
                $orderIdsPush[] = $item['order_id'];
                $dataPush[] = [
                    'order_id'         => $item['order_id'],
                    'pos_company_code' => $conditions['pos_company_code'],
                    'pos_store_code'   => $conditions['pos_store_code'],
                ];
            }

            // track the orders
            $orderIdsTrack[] = $item['order_id'];

            $data[] = $dataTemp;
        }

        return [
            'affected_rows'   => $this->selfOrder->updateManyOrdersBy($data, $conditions, $keyIndexDb),
            'data_push'       => ['data' => $dataPush, 'push_order_ids' => $orderIdsPush],
            'track_order_ids' => $orderIdsTrack
        ];
    }

    /**
     * Update or insert the POS+ Information
     *
     * @param array $params
     * @param array $headers
     * @return bool
     */
    public function savePosInfo($params = [], $headers = [])
    {
        return $this->selfOrder->savePosInfo($params, $headers);
    }

    /**
     * Update the orders by conditions for TEST
     *
     * @param array $data
     * @param array $conditions
     * @return bool
     */
    public function updateOrdersBy($data = [], $conditions = [])
    {
        // convert string of ids (1,2,3...) into an array
        if (is_array($conditions) && array_key_exists('order_id', $conditions) && is_string($conditions['order_id'])) {
            $conditions['order_id'] = explode(',', str_replace(' ', '', $conditions['order_id']));
        }
        return $this->selfOrder->updateOrdersBy($data, $conditions);
    }

    /**
     * Get one item from DB
     *
     * @param array $params
     * @param array $headers
     */
    public function getPrintStatus($params = [], $headers = [])
    {
        if (empty($params)) {
            return false;
        }

        $conditions = [
            'pos_company_code' => $params['company_code'] ?? $headers['company_code'],
            'pos_store_code'   => $params['store_code'] ?? $headers['store_no'],
            'receipt_date'     => $params['business_day']
        ];
        $fields = 'order_id,order_status, print_status, voucher';

        return $this->selfOrder->getPrintStatus($conditions, $fields);
    }
}
