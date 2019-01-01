<?php

require_once 'DBHelperTrait.php';

class Message extends CI_Model
{
    use DBHelperTrait;

    protected static $table = 'messages';

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();
    }
    
    /**
     * Get all records
     *
     * @return CI_DB_result
     */
    public function all()
    {
        return $this->db->get(self::$table)->result();
    }

    /**
     * Get all records
     *
     * @return CI_DB_result
     */
    public function getAll()
    {
        return $this->all();
    }

    /**
     * Find by id
     *
     * @param int $id [id]
     *
     * @return array
     */
    public function find($id)
    {
        return $this->db->get_where(self::$table, ['id' => $id])->result();
    }

    /**
     * Execute the sql query
     *
     * @param string $sql [sql query]
     *
     * @return mixed
     */
    public function query($sql)
    {
        try {
            return $this->db->query($sql);
        } catch (Exception $e) {
            log_message('error', $e);
            return false;
        }
    }
}
