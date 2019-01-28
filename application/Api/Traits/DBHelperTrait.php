<?php

/**
 * Helpers For Class
 */
trait DBHelperTrait
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        // load the database library if not yet
        if (!property_exists($this, 'db')) {
            $CI = &get_instance();
            $CI->load->database();
            $this->db = $CI->db;
        }
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
            log_message('error', 'Sql query: '.$sql);
            log_message('error', $e);
            return false;
        }
    }

    /**
     * Get the array of objects
     *
     * @param string $sql [sql query]
     *
     * @return mixed
     */
    public function result($sql)
    {
        $query = $this->query($sql);
        return $query !== false ? $query->result() : $query;
    }

    /**
     * Get the array of result
     *
     * @param string $sql [sql query]
     *
     * @return mixed
     */
    public function result_array($sql)
    {
        $query = $this->query($sql);
        return $query !== false ? $query->result_array() : $query;
    }

    /**
     * Get all records
     *
     * @return CI_DB_result
     */
    public function all()
    {
        return $this->db->get($this->table())->result();
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
     * @param int    $id        [id]
     * @param string $primayKey [primary key]
     *
     * @return array
     */
    public function find($id, $primayKey = null)
    {
        return $this->db->get_where($this->table(), [$this->primary_key($primayKey) => $id])->result();
    }

    /**
     * Find by conditions
     *
     * @param array $conditions []
     *
     * @return array
     */
    public function findBy($conditions = [])
    {
        if (!is_array($conditions) || empty($conditions)) {
            return null;
        }
        return $this->db->get_where($this->table(), $conditions)->result();
    }

    /**
     * Insert data
     *
     * @param array $params []
     *
     * @return bool
     */
    public function insert($params = [])
    {
        return empty($params) ? false : $this->db->insert($this->table(), $params);
    }

    /**
     * Insert multiple records
     *
     * @param array $params [[record1], [record2],...]
     *
     * @return bool
     */
    public function insertMany($params = [])
    {
        return empty($params) ? false : $this->db->insert_batch($this->table(), $params);
    }

    /**
     * Update record
     *
     * @param int   $id        []
     * @param array $params    []
     * @param array $primayKey []
     *
     * @return bool
     */
    public function update($id, $params = [], $primayKey = null)
    {
        return empty($id) || empty($params) ? false : $this->db->update($this->table(), $params, [$this->primary_key($primayKey) => $id]);
    }

    /**
     * Update multiple records
     *
     * @param int    $id         []
     * @param array  $params     []
     * @param string $primaryKey []
     *
     * @return bool
     */
    public function updateMany($id, $params = [], $primaryKey = null)
    {
        return empty($id) || empty($params) ? false : $this->db->update_batch($this->table(), $params, $this->primary_key($primayKey));
    }
    
    /**
     * Delete record
     *
     * @param int    $id         []
     * @param string $primaryKey []
     *
     * @return bool
     */
    public function delete($id, $primaryKey = null)
    {
        return empty($id) ? false : $this->db->delete($this->table(), [$this->primary_key($primaryKey) => $id]);
    }

    /**
     * Delete multiple records
     *
     * @param array $ids [array of IDs]
     *
     * @return bool
     */
    public function deleteMany($ids = [], $primaryKey = null)
    {
        if (!is_array($ids) || empty($ids)) {
            return false;
        }

        $this->db->where_in($this->primary_key($primaryKey), $ids);
        return $this->db->delete($this->table());

        //$sql = "DELETE FROM ".$this->table()." WHERE ".$primaryKey." IN (".implode(',', $ids).")";
        //return $this->query($sql);
    }

    /**
     * Get table
     *
     * @return string
     */
    public function getTable()
    {
        $table = property_exists(get_class(), 'table') ? $this->table : $this->_plural(get_class());
        if ($this->db->table_exists($table)) {
            return $table;
        }
        throw new Exception('Table ['.$table.'] does not exist in database.');
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function table()
    {
        return $this->getTable();
    }

    /**
     * Get the primary key of table
     *
     * @param bool $id []
     *
     * @return string|null
     */
    public function primary_key($id = true)
    {
        if (property_exists($this, 'primary_key')) {
            return $this->primary_key;
        }

        if (is_string($id) && !empty($id)) {
            return $id;
        }

        // get primary key from database
        $primaryKey = $this->db->primary($this->table());

        if (empty($primaryKey)) {
            return $id === true ? 'id' : null;
        }
        return $primaryKey;
    }

    /**
     * Get the columns of table
     *
     * @param string $table
     *
     * @return array
     */
    public function columns($table = null)
    {
        $table = $table ?: $this->table();
        if ($this->db->table_exists($table)) {
            return $this->db->list_fields($table);
        }
        throw new Exception('Table ['.$table.'] does not exist in database.');
    }

    /**
     * Check the fields whether they exist in the specified table of database
     *
     * @param array|string $fieldsChecked
     * @param string $table
     *
     * @return bool
     */
    public function fieldsTableExists($fieldsChecked = [], $table = null)
    {
        // the checked fields empty 
        if (is_array($fieldsChecked) && count($fieldsChecked) === 0) {
            return false;
        }

        $fieldsChecked = is_string($fieldsChecked) ? [$fieldsChecked] : array_keys($fieldsChecked);

        return empty(array_diff($fieldsChecked, $this->columns($table)));
    }

    /**
     * Plural of string
     *
     * @param string $str [string]
     *
     * @return string
     */
    private function _plural($str)
    {
        return is_string($str) ? strtolower($str)."s" : '';
    }
}
