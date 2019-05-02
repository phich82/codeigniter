<?php

use App\Api\Traits\DBHelperTrait;
use App\Api\Helpers\ApiLog;

class M_push extends CI_Model
{
    use DBHelperTrait;

    protected $table = 'mst_push';

    /**
     * Create a record or more records
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return bool|integer
     */
    public function createPush($params = [], $headers = [])
    {
        $conditions = [
            'company_code' => $params['company'],
            'store_code'   => $params['store'],
            'delete_flg'   => UNDELETE_FLAG_VALUE
        ];
        if (!$this->existPushBy($conditions)) {
            return $this->insert($this->_makePushFormat($params, $headers));
        }
        ApiLog::info('This push existed: ['.json_encode($params).']');
        return EXISTED_RECORD_VALUE;
    }

    /**
     * Create a record or more records at once
     *
     * @param  array $params
     * @param  array $headers
     *
     * @return bool
     */
    public function createPushMany($params = [], $headers = [])
    {
        $constraints = ['company_code', 'store_code'];
        $conditions  = ['delete_flg' => UNDELETE_FLAG_VALUE];
        $columnsNotUpdated = [
            'company_code',
            'store_code',
            'notification_type',
            'before_sending_time',
            'create_time',
            'create_user',
            'delete_flg',
        ];

        // 0: system error, otherwise it is total of records inserted or updated sucessfully
        return $this->updateOrInsertMany(
            $this->_makePushFormatMany($params, $headers),
            $constraints,
            $conditions,
            $columnsNotUpdated
        ) > 0;
    }

    /**
     * Check record whether it exists in database
     *
     * @param  mixed $conditions
     *
     * @return array
     */
    public function existPushBy($conditions = [])
    {
        return $this->existBy($conditions);
    }

    /**
     * Make format of push for insert
     *
     * @param  array $params
     * @param  array $headers
     * @return array
     */
    private function _makePushFormat($params = [], $headers = [])
    {
        $time   = date('YmdHis');
        $params = (array)$params;

        return [
            'pos_company_code'    => $headers['company_code'] ?? null,
            'pos_store_code'      => $headers['store_code'] ?? null,
            'company_code'        => $params['company'],
            'store_code'          => $params['store'],
            'notification_type'   => $params['notification_type'],
            'before_sending_time' => $params['before_sending_time'],
            'create_time'         => $params['create_time'] ?? $time,
            'create_user'         => $params['create_user'] ?? DB_USER_VALUE,
            'update_time'         => $params['update_time'] ?? $time,
            'update_user'         => $params['update_user'] ?? DB_USER_VALUE,
            'delete_flg'          => $params['delete_flg']  ?? UNDELETE_FLAG_VALUE,
        ];
    }

    /**
     * Make format of push for insert many
     *
     * @param  array $params
     * @param  array $headers
     * @return array
     */
    private function _makePushFormatMany($params = [], $headers = [])
    {
        return array_reduce($params, function ($carry, $item) use ($headers) {
            $carry[] = $this->_makePushFormat($item, $headers);
            return $carry;
        });
    }
}
