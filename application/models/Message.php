<?php

class Message extends CI_Model
{
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
     * find by id
     *
     * @param int $id [id]
     *
     * @return array
     */
    public function find($id)
    {
        $sql = "SELECT * FROM ".self::$table." WHERE id={$id}";
        $query = $this->query($sql);
        return $query !== false ? $query->result() : $query;
    }

    /**
     * query
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
