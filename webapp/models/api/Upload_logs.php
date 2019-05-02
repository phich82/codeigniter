<?php

use App\Api\Traits\DBHelperTrait;

class Upload_logs extends CI_Model
{
    use DBHelperTrait;

    protected $table = 'mst_pos';

    /**
     * Get Company & Store of Reservation Cloud from POS Info
     *
     * @param string $posCompany Company Code of POS System
     * @param string $posStore   Store Code of POS System
     * @return Array
     */
    public function getCompanyByPOS($posCompany, $posStore) {
        $posInfo = $this->db->select('company_code, store_no')->where(array(
            'pos_company_code' => $posCompany,
            'pos_store_code'   => $posStore,
            'delete_flg'       => 0
        ))->get($this->table());
        return ($posInfo->num_rows() > 0) ? $posInfo->row_array() : [];
    }

}
