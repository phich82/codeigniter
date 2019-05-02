<?php

require_once(dirname(__FILE__) . '/../../controllers/apis/ApiController.php');
require_once(dirname(__FILE__) . '/../../Api/Helpers/HttpCode.php');
require_once(dirname(__FILE__) . '/../../Api/Helpers/ApiLog.php');

use App\Api\Helpers\HttpCode;
use App\Api\Helpers\ApiLog;

class ApiRsvCloudController extends ApiController
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api/Self_orders_service', null, 'selfOrdersService');
        $this->load->library('api/M_push_service', null, 'mstPushService');
    }

    /**
     * Get the orders from Rsv Sales and save them to RSV Cloud
     *
     * @return object
     */
    public function createOrdersPost()
    {
        $result = $this->selfOrdersService->createOrders(...$this->_arguments());

        $response = [
            $this->response_result_key => $this->success_status
        ];

        if ($result === false) {
            $response[$this->response_result_key] = $this->error_status;
            $response[$this->response_error_key]  = $this->api_default_error_message;
        }

        return $this->response->json($response, HttpCode::HTTP_OK);
    }

    /**
     * Get and update the orders
     *
     * @return object
     */
    public function getOrdersPost()
    {
        $result = $this->selfOrdersService->getOrdersList(...$this->_arguments());
        $statusUpdate = ($result['update_status'] === true) ? $this->success_status : $this->error_status;

        $response = [
            $this->response_update_status_key => $statusUpdate,
            $this->response_result_key        => $this->warning_status,
            $this->response_data_key          => $result['orders'],
        ];

        if (!empty($result['orders'])) {
            $response[$this->response_result_key] = $this->success_status;
            $response[$this->response_data_key]   = $result['orders']['list'];
        }

        return $this->response->json($response, HttpCode::HTTP_OK);
    }

    /**
     * Create push data
     *
     * @return object
     */
    public function createPushPost()
    {
        $result = $this->mstPushService->createPush(...$this->_arguments());

        $response = [
            $this->response_result_key => $this->success_status
        ];

        // system error
        if ($result === false) {
            $response[$this->response_result_key] = $this->error_status;
            $response[$this->response_error_key]  = $this->api_default_error_message;
        }

        // already existed
        if ($result === $this->api_existed_record_value) {
            $response[$this->response_result_key] = $this->error_status;
            $response[$this->response_error_key]  = $this->api_default_exist_message;
        }

        return $this->response->json($response, HttpCode::HTTP_OK);
    }

    /**
     * Get and save the information of the POS+ when connected to it.
     */
    public function savePosInfoPost()
    {
        $result = $this->selfOrdersService->savePosInfo(...$this->_arguments());

        return $this->response->json([
            $this->response_result_key  => $result ? $this->success_status : $this->error_status,
            $this->response_message_key => $result ? $this->response_success_text : $this->response_failed_text,
        ], HttpCode::HTTP_OK);
    }

    /**
     * Send the  order(s) to POS+
     * For test: Send data to POS+.
     *
     *  0: Not sent.
     *  1: Sent Success.
     *  2: Sent Fail.
     * @return object
     */
    public function addReservePost()
    {
        // get the resource by the request method
        $body    = $this->_body();
        $headers = $this->_headers();

        $orders = $body['orders'];
        if (empty($orders)) {
            return $this->response->json([
                $this->response_result_key => $this->error_status,
                $this->response_error_key  => HttpCode::message(HttpCode::HTTP_BAD_REQUEST)
            ], HttpCode::HTTP_OK);
        }

        $conditions = [
            'pos_company_code' => $body['company_code'] ?? $headers['company_code'],
            'pos_store_code'   => (string)($body['store_code'] ?? $headers['store_code']),
        ];

        $failedOrders = [];
        $cnt = 0;
        $response = array_reduce($orders, function ($carry, $item) use ($conditions, &$failedOrders, &$cnt) {
            $sent_to  = $item['status'];
            $voucher  = ($sent_to == 5) ? $item['reserve_date'].'0000000'. $cnt++ : '';
            $postData = [
                'sent_status'  => 1,
                'print_status' => 1,
            ];

            $postData['voucher'] = $voucher ?: ($item['reserve_date'].'0000000'. $cnt++);

            $conditions['order_id'] = $item['order_id'];

            // update order. If error, log this error
            if ($this->selfOrdersService->UpdateOrdersBy($postData, $conditions) === false) {
                $failedOrders[] = $item['order_id'];
            }

            ApiLog::info('['.__FUNCTION__.'] Updated delivery status ================================> '.json_encode($item));

            $carry[] = [
                'order_id'        => $item['order_id'],
                'voucher_number'  => $postData['voucher'],
                'sent_status'  => 1,
                'print_status' => 1,
                'already_existed' => false,
            ];
            return $carry;
        });

        $response = [
            $this->response_result_key => empty($failedOrders) ? $this->success_status : $this->error_status,
            $this->response_order_result_key => $response
        ];

        if (!empty($failedOrders)) {
            $response[$this->response_failed_orders_key] = $failedOrders;
            ApiLog::info('['.__FUNCTION__.'] Orders not exist: ================================> '.json_encode($failedOrders));
        }

        return $this->response->json($response, HttpCode::HTTP_OK);
    }

    /**
     * This function for get print status of order by ID
     * @package DEMO
     *
     * @return object
     */
    public function getKitchenPrintResultPost()
    {
        // get the print statues
        $result = $this->selfOrdersService->getPrintStatus(...$this->_arguments());

        // error
        if ($result === false) {
            return $this->response->json([
                $this->response_result_key => $this->error_status,
                $this->response_error_key  => HttpCode::message(HttpCode::HTTP_BAD_REQUEST)
            ], HttpCode::HTTP_OK);
        }

        // mapping & mock data
        $cnt = 0;
        $mapping = array_map(function ($item) use (&$cnt){
            $print_status = ($item['order_status'] == 0 || $item['order_status'] == 1 || $item['order_status'] == 5) ? 0 : rand(1,2);
            $payment_status = array(10,50);
            return [
                'order_id'             => $item['order_id'],
                'voucher_number'       => $item['voucher'] ?: date('Ymd').'000000'.$cnt++,
                'kitchen_print_result' => $print_status,
                'payment_status' => $payment_status[rand(0,1)]
            ];
        }, $result ?: []);

        // success
        return $this->response->json([
            $this->response_result_key => $this->success_status,
            'print_result' => $mapping,
        ], HttpCode::HTTP_OK);
    }

}
