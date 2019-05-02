<?php

use App\Api\Traits\DBHelperTrait;

class T_push_notification extends CI_Model
{
    use DBHelperTrait;

    protected $table = 'trn_push_notification';

    /**
     * Create an new record
     *
     * @param  array $params
     *
     * @return mixed
     */
    public function createPushNotification($params = [])
    {
        return $this->insert($params);
    }

    /**
     * Update one or more records at once time
     *
     * @param  array $params
     * @param  array $conditions
     *
     * @return bool
     */
    public function updatePushNotificationBy($params = [], $conditions = [])
    {
        return $this->updateBy($params, $conditions);
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
     * Check the push notification whether it exists in database by conditions
     *
     * @param  array $conditions
     *
     * @return bool
     */
    public function isExistedBy($conditions = [])
    {
        return !empty($this->existBy($conditions));
    }

    /**
     * Get the store detail by the company code and the store code
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return array
     */
    public function getStoreDetail($params = [], $headers = [])
    {
        $pos_company = $params['pos_company_code'] ?? $headers['company_code'];
        $pos_store   = $params['pos_store_code']   ?? $headers['store_code'];

        $this->db->select("p.company_code, p.pos_company_code, p.store_no, p.pos_store_code, s.lang_code, s.self_order_title as title, s.self_order_sending_time as time_before_send, s.self_order_desc as description, to_timestamp(concat(o.receipt_date, o.receipt_time),'yyyymmddhh24miss') as reserve_date, o.order_id, o.connect_customer_code");
        $this->db->from('mst_pos as p');
        $this->db->join('mst_store as s', '(p.company_code = s.company_code AND p.store_no = s.store_no)');
        $this->db->join('trn_self_orders as o', '(o.pos_company_code = p.pos_company_code AND o.pos_store_code = p.pos_store_code)');
        $this->db->where(array(
            'p.pos_company_code' => $pos_company,
            'p.pos_store_code'   => $pos_store,
            'o.order_id'         => $params['order_id']
        ))->where("(o.receipt_date notnull or o.receipt_date != '') AND (o.receipt_time notnull or o.receipt_time != '')");

        $row = $this->db->get();

        return !empty($row) ? $row->row_array() : [];
    }
}
