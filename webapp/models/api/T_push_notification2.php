<?php

use App\Api\Traits\DBHelperTrait;

class T_push_notification extends CI_Model
{
    use DBHelperTrait;

    protected $table = 'trn_push_notification';

    /**
     * Update or insert one or more records at once time
     *
     * @param  array $params
     * @param  array $headers
     * @param  array $columnsNotUpdated
     * @param  array|bool $append
     * @param  integer $batch
     *
     * @return bool
     */
    public function updateOrInsertPushNotificationtMany($params = [], $headers = [], $columnsNotUpdated = [], $append = true, $batch = 100)
    {
        $constraints = ['pos_company_code', 'pos_store_code', 'order_id', 'message_no'];
        $conditions  = ['delete_flg' => UNDELETE_FLAG_VALUE];
        $columnsNotUpdated = !empty($columnsNotUpdated) ? $columnsNotUpdated : [
            'pos_company_code',
            'pos_store_code',
            'order_id',
            'message_no',
            'contents',
            'sent_date',
            'sent_status',
            'create_time',
            'create_user',
            'delete_flg',
        ];

        $dataAppend = [];
        // append the default data, but no make new params
        if ($append === true) {
            $data = $params;
            $dataAppend = $this->getDefaultAppendData();
        }
        // no append any data, but no make new params
        elseif ($append === false) {
            $data = $params;
        }
        // append the specified data, but no make new params
        elseif (is_array($append) && count($append) > 0) {
            $data = $params;
            $dataAppend = $append;
        }
        // no append any data, but make new params
        else {
            $data = $this->_makePushNotificationFormatMany($params, $headers);
        }

        // 0: system error, otherwise it is total of records inserted or updated sucessfully
        return $this->updateOrInsertMany(
            $data,
            $constraints,
            $conditions,
            $columnsNotUpdated,
            $dataAppend,
            $batch
        ) > 0;
    }

    /**
     * Get the message no
     *
     * @param  array $params
     *
     * @return int
     */
    public function message_no($params = [])
    {
        $rowMax = $this->db->select_max('message_no')->where($params)->get($this->table());

        if ($rowMax->num_rows() <= 0) {
            return 1;
        }

        $result = $rowMax->row_array();

        return intval($result['message_no']) + 1;
    }

    /**
     * Get stores with orders by pos_company_code, pos_store_code, order_id
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return array
     */
    public function getStores($params = [], $headers = [])
    {
        $pos_company = $params['pos_company_code'] ?? $headers['company_code'];
        $pos_store   = $params['pos_store_code']   ?? $headers['store_code'];
        $orderIds    = is_array($params['order_ids']) ? $params['order_ids'] : [$params['order_ids']];

        $this->db->select("n.sent_status as notify_status, p.company_code, p.pos_company_code, p.store_no, p.pos_store_code, s.lang_code, s.self_order_title as title, s.self_order_sending_time as time_before_send, s.self_order_desc as description, to_timestamp(concat(o.receipt_date, o.receipt_time),'yyyymmddhh24miss') as reserve_date, o.order_id, o.connect_customer_code");
        $this->db->from('trn_self_orders as o');
        $this->db->join('mst_pos as p', '(o.pos_company_code = p.pos_company_code AND o.pos_store_code = p.pos_store_code)');
        $this->db->join('mst_store as s', '(p.company_code = s.company_code AND p.store_no = s.store_no)');
        $this->db->join('trn_push_notification as n', '(n.pos_company_code = o.pos_company_code AND n.pos_store_code = o.pos_store_code AND n.order_id = o.order_id)', 'left');
        $this->db->where(['o.pos_company_code' => $pos_company, 'o.pos_store_code' => $pos_store]);
        $this->db->where_in('o.order_id', $orderIds);
        $this->db->where("(o.receipt_date notnull or o.receipt_date != '') AND (o.receipt_time notnull or o.receipt_time != '')");

        $row = $this->db->get();

        return !empty($row) ? $row->result_array() : [];
    }

    /**
     * Get the push notifications
     *
     * @param  integer $limit
     *
     * @return array
     */
    public function getPushNotifications($limit = 100)
    {
        return $this->db->select("pos_company_code, pos_store_code, order_id, contents, sent_date, sent_status")
            ->where([
                'delete_flg'     => UNDELETE_FLAG_VALUE,
                'sent_status !=' => CONNECT_CLOUD_RESPONSE_SUCCESS
            ])
            ->order_by('create_time', 'DESC')
            ->limit($limit)
            ->get($this->table())
            ->result_array();
    }

    /**
     * Get the default data will be appended
     *
     * @return array
     */
    public function getDefaultAppendData()
    {
        $time = date('YmdHis');
        return [
            'message_no'          => 1,
            'contents'            => null,
            'sent_date'           => '',
            'sent_status'         => CONNECT_CLOUD_NOT_RESPONSE,
            'create_time'         => $time,
            'create_user'         => DB_USER_VALUE,
            'update_time'         => $time,
            'update_user'         => DB_USER_VALUE,
            'delete_flg'          => UNDELETE_FLAG_VALUE,
        ];
    }

    /**
     * Make format of push notification for insert
     *
     * @param  array $params
     * @param  array $headers
     * @return array
     */
    private function _makePushNotificationFormat($params = [], $headers = [])
    {
        $time   = date('YmdHis');
        $params = (array)$params;
        return [
            'pos_company_code'    => $params['pos_company_code'] ?? $headers['company_code'] ?? null,
            'pos_store_code'      => $params['pos_store_code']   ?? $headers['store_code']   ?? null,
            'order_id'            => $params['order_id'],
            'message_no'          => 1,
            'contents'            => $params['contents']    ?? null,
            'sent_date'           => $params['sent_date']   ?? '',
            'sent_status'         => $params['sent_status'] ?? CONNECT_CLOUD_NOT_RESPONSE,
            'create_time'         => $params['create_time'] ?? $time,
            'create_user'         => $params['create_user'] ?? DB_USER_VALUE,
            'update_time'         => $params['update_time'] ?? $time,
            'update_user'         => $params['update_user'] ?? DB_USER_VALUE,
            'delete_flg'          => $params['delete_flg']  ?? UNDELETE_FLAG_VALUE,
        ];
    }

    /**
     * Make format of push notification for insert many
     *
     * @param  array $params
     * @param  array $headers
     * @return array
     */
    private function _makePushNotificationFormatMany($params = [], $headers = [])
    {
        return array_reduce($params, function ($carry, $item) use ($headers) {
            $carry[] = $this->_makePushNotificationFormat($item, $headers);
            return $carry;
        });
    }

    /**
     * Create an new record
     *
     * @param  array $params
     *
     * @return mixed
     */
    // public function createPushNotification($params = [])
    // {
    //     return $this->insert($params);
    // }

    /**
     * Create many records at once time
     *
     * @param  array $params
     *
     * @return mixed
     */
    // public function createPushNotificationMany($params = [])
    // {
    //     return $this->insertMany($params);
    // }

    /**
     * Update one or more records at once time
     *
     * @param  array $params
     * @param  array $conditions
     *
     * @return bool
     */
    // public function updatePushNotificationBy($params = [], $conditions = [])
    // {
    //     return $this->updateBy($params, $conditions);
    // }

    /**
     * Check the push notification whether it exists in database by conditions
     *
     * @param  array $conditions
     *
     * @return bool
     */
    // public function isExistedBy($conditions = [])
    // {
    //     return !empty($this->existBy($conditions));
    // }

    /**
     * Get the store detail by the company code and the store code
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return array
     */
    // public function getStoreDetail($params = [], $headers = [])
    // {
    //     $pos_company = $params['pos_company_code'] ?? $headers['company_code'];
    //     $pos_store   = $params['pos_store_code']   ?? $headers['store_no'];

    //     $this->db->select("p.company_code, p.pos_company_code, p.store_no, p.pos_store_code, s.lang_code, s.self_order_title as title, s.self_order_sending_time as time_before_send, s.self_order_desc as description, to_timestamp(concat(o.receipt_date, o.receipt_time),'yyyymmddhh24miss') as reserve_date, o.order_id, o.connect_customer_code");
    //     $this->db->from('mst_pos as p');
    //     $this->db->join('mst_store as s', '(p.company_code = s.company_code AND p.store_no = s.store_no)');
    //     $this->db->join('trn_self_orders as o', '(o.pos_company_code = p.pos_company_code AND o.pos_store_code = p.pos_store_code)');
    //     $this->db->where(array(
    //         'p.pos_company_code' => $pos_company,
    //         'p.pos_store_code'   => $pos_store,
    //         'o.order_id'         => $params['order_id']
    //     ))->where("(o.receipt_date notnull or o.receipt_date != '') AND (o.receipt_time notnull or o.receipt_time != '')");

    //     $row = $this->db->get();

    //     return !empty($row) ? $row->row_array() : [];
    // }
}
