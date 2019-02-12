<?php

require_once 'DBHelperTrait.php';

class Message extends CI_Model
{
    use DBHelperTrait;

    protected $table = 'messages';
    protected $primary_key = 'id';

    /**
     * Get messages by date
     *
     * @param string $date [date string]
     *
     * @return array
     */
    public function getMessagesByDate($date)
    {

    }

    /**
     * Delete messages
     *
     * @param array $ids [array of IDs]
     *
     * @return void
     */
    public function deleteMessages($ids = [])
    {
        // Note: transaction for only DB Engine is INNODB
        $this->db->trans_begin();

        $this->deleteMany($ids);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }
}
