<?php

use App\Api\Traits\DBHelperTrait;

class Self_order extends CI_Model
{
    use DBHelperTrait;

    protected $table = 'trn_self_orders';

    /**
     * Get the orders list
     *
     * @param array $params
     * @param array $headers
     * @return array
     */
    public function getOrdersList($params = [], $headers = [])
    {
        $company = $params['company_code'] ?? $headers['company_code'];
        $store   = $params['store_code'] ?? $headers['store_code'];
        $limit   = ROWS_PER_PAGE;
        $offset  = 0;
        $hasBusinessHour = false;

        $this->db->select('*')->where([
            'pos_company_code' => $company,
            'pos_store_code'   => $store,
            'delete_flg'       => UNDELETE_FLAG_VALUE
        ]);

        $this->db->where_in('receipt_date', explode(',', trim($params['order_receive'])));

        // without getting all
        $isNotGetAll = !isset($params['get_all']) || in_array($params['get_all'], ['false', false]);

        if ($isNotGetAll) {
            // check the bussiness hour
            $hasBusinessHour = in_array($params['business_hour'] ?? null, ['true', true], true);
            // only get the orders with resevered (0) or cancelled (5) status
            $this->db->where_in('order_status', [ORDER_STATUS_RESERVED, ORDER_STATUS_CANCELLED]);
            // check the limitation of the returned records
            if (isset($params['order_limit'])) {
                $limit = (int)$params['order_limit'];
            }
            // for paging
            if (isset($params['page'])) {
                $page   = (int)$params['page'];
                $offset = $page < 1 ? 0 : ($page - 1) * $limit;
            }
        }

        // get the orders list
        $orders = $this->db->limit($limit, $offset)->get($this->table());

        // empty
        if ($orders->num_rows() <= 0) {
            return [];
        }

        $response = [
            'list' => [
                'company_code'  => $company,
                'store_no'      => $store,
                'calendar_date' => $params['order_receive'] ?? '',
                'request_time'  => date('Y-m-d H:i:s'),
                'orders'        => [],
            ],
        ];

        if ($hasBusinessHour) {
            $response['list']['business_hour'] = $this->getBusinessHour($company, $store);
        }

        // get the currency by store and company
        $currency = $this->_getCurrency($company, $store);

        foreach ($orders->result_array() as $order) {
            $response['list']['orders'][] = $this->getResponseFormat($order, $currency);
        }

        return $response;
    }

    /**
     * Update the orders by conditions
     *
     * @param array $data
     * @param array $conditions
     * @return bool|int
     */
    public function updateOrdersBy($data = [], $conditions = [])
    {
        foreach ($conditions as $column => $value) {
            $method = is_array($value) ? 'where_in' : 'where';
            $this->db->{$method}($column, $value);
        }

        if ($data['order_status'] == 5) {
            $canceldata = array (
                "pos_company_code" => $data['pos_company_code'],
                "pos_store_code" => $data['pos_store_code'],
                "order_id" => $data['order_id'],
                "order_status" => $data['order_status'],
                "connect_customer_code" => $data['connect_customer_code'],
                "more_info" => $data['more_info'],
                "update_time" => $data['update_time'],
                "update_user" => $data['update_user']
            );

            return $this->db->update($this->table(), $canceldata) === false ? false : $this->affectedRows();
        }

        return $this->db->update($this->table(), $data) === false ? false : $this->affectedRows();
    }

    /**
     * Update the multiple rows at once time by conditions
     *
     * @param array $params
     * @param array $headers
     * @param string $index (field name)
     *
     * @return integer
     */
    public function updateManyOrdersBy($data = [], $conditions = [], $index = null)
    {
        return $this->selfOrder->updateManyBy($data, $conditions, $index);
    }

    /**
     * Get and format business hour
     *
     * @param string $companyCode
     * @param string $storeNo
     */
    public function getBusinessHour($companyCode, $storeNo)
    {
        $this->load->model('postas/m_pos');
        $pos = $this->m_pos->getCompanyByPOS($companyCode, $storeNo);
        $carry = [];
        if (!empty($pos)) {
            $hours = $this->db->select('h.day, h.start_time, h.end_time, h.index, c.day_status')
                ->from('mst_business_hour h')
                ->join(' mst_calendar c', '(h.company_code = c.company_code and h.store_no = c.store_no)')
                ->where([
                    'h.company_code' => $pos['company_code'],
                    'h.store_no'     => $pos['store_no'],
                    'h.delete_flg'   => UNDELETE_FLAG_VALUE,
                    'c.day_status'   => RSV_STATUS_DAY_BUSSINESS
                ])
                ->order_by('h.day')
                ->order_by('h.index')
                ->group_by('h.day, h.start_time, h.end_time, h.index, c.day_status')
                ->get()
                ->result_array();
            if(!empty($hours)) {
                for($i = 0; $i < count($hours); $i += 2) {
                    $lunch  = '';
                    $dinner = '';
                    if ($hours[$i]['index'] == LUNCH_TIME_VALUE) {
                        $lunch  = $hours[$i]['start_time'].','.$hours[$i]['end_time'];
                        $dinner = isset($hours[$i + 1]) ? $hours[$i + 1]['start_time'].','.$hours[$i + 1]['end_time'] : '';
                    } elseif ($hours[$i]['index'] == DINNER_TIME_VALUE) {
                        $dinner = $hours[$i]['start_time'].','.$hours[$i]['end_time'];
                        $lunch  = isset($hours[$i + 1]) ? $hours[$i + 1]['start_time'].','.$hours[$i + 1]['end_time'] : '';
                    }
                    $carry[] = [
                        'lunch_time'  => $lunch,
                        'dinner_time' => $dinner,
                        'day_status'  => $hours[$i]['day_status'],
                        'day_in_week' => $hours[$i]['day']
                    ];
                }
            }
        }

        return $carry;
    }

    /**
     * Insert one or more orders at once time
     *
     * @param array $params
     * @return bool
     */
    public function save($params = [], $headers = [])
    {
        $failed_orders  = [];
        $success_orders = [];
        $orders = $params['orders'] ?? [];

        foreach ($orders as $order) {
            if ($this->updateOrInsert($order, $headers)) {
                $success_orders[] = $order['order_id'];
            } else {
                $failed_orders[] = $order['order_id'];
            }
        }

        // all orders inserted are failed
        if (!empty($orders) && count($failed_orders) === count($orders)) {
            return false;
        }

        return count($failed_orders) === 0;
    }

    /**
     * Update Or Insert the order
     *
     * @param array $params
     * @param array $headers
     * @return bool
     */
    public function updateOrInsert($params = [], $headers = [])
    {
        $data = $this->makeOrderFormat($params, $headers);
        $condition = [
            'order_id'         => $data['order_id'],
            'pos_company_code' => $data['pos_company_code'],
            'pos_store_code'   => $data['pos_store_code'],
            'delete_flg'       => UNDELETE_FLAG_VALUE
        ];
        // update if exist
        if (!empty($this->existBy($condition))) {
            $data['sent_status'] = DEFAULT_SENT_STATUS_VALUE;

            return $this->updateOrdersBy($data, $condition) > 0;
        }
        // insert
        return $this->insert($data) > 0;
    }

    /**
     * Save the POS+ information
     *
     * @param array $params
     * @param array $headers
     * @return bool
     */
    public function savePosInfo($params = [], $headers = [])
    {
        $table = 'trn_pos_connect';

        $conditions = [
            'company_code' => $params['company_code'] ?? $headers['company_code'],
            'store_no'     => $params['store_no'] ?? $headers['store_no']
        ];
        $time = date('YmdHis');
        $data = [
            'pos_company_code' => $params['pos_company_code'],
            'pos_company_name' => $params['pos_company_name'],
            'pos_store_code'   => $params['pos_store_code'],
            'pos_store_name'   => $params['pos_store_name'],
            'business_date'    => $params['business_date'] ?: date('Ymd'),
            'business_time'    => $params['business_time'] ?: date('His'),
            'ip_address'       => $params['ip_address'] ?: '',
            'app_version'      => $params['app_version'] ?: '',
            'status'           => $params['status'] ?: DEFAULT_CONNECT_POS_STATUS,
            'device_id'        => $params['device_id'] ?: '',
            'create_time'      => $params['create_time'] ?? $time,
            'create_user'      => $params['create_user'] ?? DB_USER_VALUE,
            'update_time'      => $params['update_time'] ?? $time,
            'update_user'      => $params['update_user'] ?? DB_USER_VALUE,
            'delete_flg'       => $params['delete_flg']  ?? UNDELETE_FLAG_VALUE,
        ];

        $userId = (int)($params['user_id'] ?? $headers['user_id']);
        if (!empty($userId)) {
            $data['user_id'] = $userId;
        }

        // clear the old information
        $this->deleteBy($conditions, $table);

        // insert the new information
        return $this->insert(array_merge($conditions, $data), $table);
    }

    /**
     * Get one item from DB
     *
     * @param array $conditions
     * @param string $fields
     */
    public function getPrintStatus($conditions = [], $fields = '*') {
        // === build query === //
        $this->db->select($fields)->from($this->table());
        if (!empty($conditions)) {
            $this->db->where($conditions);
        }
        // === end - build query === //
        return $this->db->get()->result_array();
    }

    /**
     * Make the order by format from input
     *
     * Order statues:
     * 1: Comfirmed; 2: Cooking; 3: Cancelled; 4: Accepted; 5: Cooked; 6: Delivering; 7: Complete
     *
     * @param array $params
     * @return array
     */
    private function makeOrderFormat($params = [], $headers = [])
    {
        $time   = date('YmdHis');
        $params = (array)$params;

        $moreInfo = [
            'total_price'      => $params['total_price'],
            'rsv_order_status' => $params['rsv_order_status'],
        ];

        $orderStatus = $params['order_status'] ?? DEFAULT_ORDER_STATUS;

        // check the order status cancelled from RSV Sales
        if ($params['rsv_order_status'] == RSV_ORDER_STATUS_CANCELLED) {
            $orderStatus = ORDER_STATUS_CANCELLED;
        }

        $reserveDate = strtotime($params['reserve_date']);

        return [
            'order_id'              => $params['order_id'],
            'ope_id'                => $params['ope_id'],
            'connect_customer_code' => $params['customer_code'],
            'first_name_kana'       => $params['first_name_kana'],
            'last_name_kana'        => $params['last_name_kana'],
            'first_name'            => $params['first_name'],
            'last_name'             => $params['last_name'],
            'customer_phone'        => $params['customer_phone'],
            'payment_method'        => $params['payment_method'],
            'receipt_date'          => date('Ymd', $reserveDate),
            'receipt_time'          => date('His', $reserveDate),
            'order_date'            => date('YmdHis', strtotime($params['order_datetime'] ?? time())),
            'contact_matter'        => $params['contact_matter'] ?? null,
            'from'                  => $params['from'],
            'auto_print_time'       => $params['auto_print_time'],
            'cancel_deadline_date'  => $params['cancel_deadline_date'],
            'more_info'             => json_encode($moreInfo),
            'details'               => json_encode($params['details'] ?: []),
            'order_status'          => $orderStatus,
            'pos_company_code'      => $params['company_code'] ?? $headers['company_code'],
            'pos_store_code'        => $params['store_code'] ?? $headers['store_no'],
            'sent_status'           => UNSENT_TO_VALUE,
            'create_time'           => $params['create_time'] ?? $time,
            'create_user'           => $params['create_user'] ?? DB_USER_VALUE,
            'update_time'           => $params['update_time'] ?? $time,
            'update_user'           => $params['update_user'] ?? DB_USER_VALUE,
            'delete_flg'            => $params['delete_flg']  ?? UNDELETE_FLAG_VALUE,
        ];
    }

    /**
     * Get the response template
     *
     * @param array $order
     * @param array $currency
     *
     * @return array
     */
    private function getResponseFormat($order = [], $currency = [])
    {
        $order    = (array)$order;
        $template = [
            'order_id'                  => $order['order_id'],
            'order_status'              => $order['order_status'],
            'company_code'              => $order['pos_company_code'],
            'store_currency'            => $currency['currency'],
            'store_currency_name'       => $currency['name'],
            'store_currency_decimal'    => intval($currency['decimal']),
            'first_name_kana'           => $order['first_name_kana'],
            'last_name_kana'            => $order['last_name_kana'],
            'first_name'                => $order['first_name'],
            'last_name'                 => $order['last_name'],
            'customer_phone'            => $order['customer_phone'],
            'sent_status'               => $order['sent_status'],
            'voucher_number'            => $order['voucher'],
            'print'                     => $order['print_status'],
            'reserve_date'              => $order['receipt_date'],
            'reserve_time'              => $order['receipt_time'],
            'order_date'                => substr($order['order_date'], 0, 8),
            'order_time'                => substr($order['order_date'], -6),
            'payment_method'            => $order['payment_method'],
            'contact_matter'            => $order['contact_matter'],
            'ope_id'                    => $order['ope_id'] ?? '',
            'details'                   => !empty($order['details']) ? json_decode($order['details'], true) : [],
            'from'                      => $order['from'],
            'auto_print_time'           => $order['auto_print_time'],
            'cancel_deadline_date'      => $order['cancel_deadline_date'],
        ];

        if (!empty($order['more_info'])) {
            $template = array_merge(json_decode($order['more_info'] ?: [], true), $template);
        }

        return $template;
    }

    /**
     * Get the currency by the company code and the store code
     *
     * @param  string $company_code
     * @param  string $store_code
     *
     * @return array
     */
    private function _getCurrency($pos_company, $pos_store)
    {
        $this->db->select("c.currency_code as currency, c.currency_short_name as name, c.dicimal_digit as decimal");
        $this->db->from('mst_pos as p');
        $this->db->join('mst_store as s', '(p.company_code = s.company_code AND p.store_no = s.store_no)');
        $this->db->join('mst_currency as c', 's.currency_code = c.currency_code');
        $this->db->where(['p.pos_company_code' => $pos_company, 'p.pos_store_code' => $pos_store]);

        $row = $this->db->get();

        return !empty($row) ? $row->row_array() : ['currency' => '', 'name' => '', 'decimal' => 0];
    }

    /**
     * Check today
     *
     * @param mixed  $timestamp
     * @param string $company
     * @param string $store
     *
     * @return void
     */
    private function checkToday($timestamp = null, $company = '', $store = '')
    {
        $timestamp = is_string($timestamp) ? strtotime($timestamp) : $timestamp;
        $date = date('Y-m-d', $timestamp);
        // check date == curent date
        $diff = date_diff(date_create($date), date_create(date('Y-m-d', time())))->format('%a');
        if ($diff > 0) {
            return 0;
        }
        // check time
        $hour = date('H', $timestamp);
        $day  = (date('N', strtotime('-1 day')) - 1);
        if ($hour > 6) {
            return 1;
        }
        // check in ddatabase
        $business_hour = $this->db->select('start_time, end_time')
            ->where([
                'company_code' => $company,
                'store_no'     => $store,
                'day'          => $day,
                'delete_flg'   => UNDELETE_FLAG_VALUE,
                'index'        => DINNER_TIME_VALUE,
            ])
            ->get('mst_business_hour')
            ->row_array();
        return (!empty($business_hour) && (($hour + 24) > substr($business_hour['end_time'], 0, 2))) ? 1 : 0;
    }
}
