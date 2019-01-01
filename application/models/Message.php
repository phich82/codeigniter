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
     * find by id
     *
     * @param int $id [id]
     *
     * @return array
     */
    public function find($id)
    {
        $sql = "SELECT * FROM ".self::$table." WHERE id={$id}";
        return $this->result($sql);
    }
}
