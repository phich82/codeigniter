<?php
/**
* @author Huynh Phat <phat.nguyen@persol.co.jp>
* @license [v1]
*/
namespace App\Api\Traits;

use Exception;
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
     * Check whether the given record exists in database
     *
     * @param  array  $conditions
     * @param  string $select
     *
     * @return array
     */
    public function existBy($conditions = [], $select = '*')
    {
        $exist = $this->db->select($select)->where($conditions)->get($this->table());
        return $exist ? $exist->row_array() : [];
    }

    /**
     * Get total of records of table
     *
     * @return int
     */
    public function total()
    {
        return $this->totalBy();
    }

    /**
     * Get total of records of table
     *
     * @param  array $conditions
     *
     * @return int
     */
    public function totalBy($conditions = [])
    {
        // for soft delete
        if (!empty($conditions)) {
            $this->db->where($conditions);
        }
        return $this->db->get($this->table())->num_rows();
    }

    /**
     * Get the affected rows when inserted, updated, deleted
     *
     * @return int
     */
    public function affectedRows()
    {
        return $this->db->affected_rows();
    }

    /**
     * Insert data
     *
     * @param array $params []
     *
     * @return bool
     */
    public function insert($params = [], $table = null)
    {
        $table = $table ?: $this->table();
        return empty($params) ? false : $this->db->insert($table, $params);
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
        return empty($params) ? false : $this->db->insert_batch($this->table(), $params) > 0;
    }

    /**
     * Update or insert using upsert only for one record
     *
     * @param  array $params
     * @param  array $constraints
     * @param  array $conditions
     * @param  string $table
     * @param  string $prefix
     *
     * @return bool
     */
    public function updateOrInsert($params = [], $constraints = [], $conditions = [], $table = '', $prefix = '')
    {
        return $this->updateOrInsertMany([$params], $constraints, $conditions, 1, $table, $prefix);
    }

    /**
     * Update or insert using upsert only for one record
     *
     * @param  array $params
     * @param  array $conditions
     * @param  string $upsert
     * @param  string $table
     * @param  string $prefix
     *
     * @return bool
     */
    public function updateOrInsertUseUpsert($params = [], $conditions = [], $upsert = 'upsert', $table = '', $prefix = '')
    {
        $table  = $table ?: $this->table();
        $params = $this->_prepareParams($params);
        $where  = $this->_prepareWhere($conditions, '', $prefix);
        $upsertWhere = $this->_prepareWhere($conditions, $upsert, $prefix);

        $sql = "WITH $upsert AS (
			UPDATE $table SET (".implode(', ', $params['fields']).") = (".implode(', ', $params['values']).")
			$where
			RETURNING *
		)
		INSERT INTO $table (".implode(', ', $params['fields']).")
		SELECT ".implode(', ', $params['values'])."
        WHERE NOT EXISTS (SELECT 1 FROM $upsert $upsertWhere)";

        $this->db->query($sql);
        // affected_rows: 0 => updated successflly, 1 => inserted successfully
        return $this->db->affected_rows();
    }

    /**
     * Prepare parameters
     *
     * @param  array  $params
     * @param  string $table
     * @param  string $prefix
     *
     * @return array
     */
    private function _prepareParams($params = [], $table = '', $prefix = '')
    {
        $fields = [];
        $values = [];
        $prefix = (!empty($table) ? $table.'.' : '').$prefix;
        foreach ($params ?: [] as $column => $value) {
            $fields[] = '"'.$prefix.$column.'"';
            $values[] = $this->db->escape($value);
        }
        return ['fields' => $fields, 'values' => $values];
    }

    /**
     * Update or insert multiple records at once time
     *
     * @param  array  $data
     * @param  array  $constraints
     * @param  array  $conditions
     * @param  array  $columnsNotUpdated
     * @param  array  $dataAppend
     * @param  int    $batch
     * @param  string $table
     * @param  string $prefix
     *
     * @return bool|int
     */
    public function updateOrInsertMany($data = [], $constraints = [], $conditions = [], $columnsNotUpdated = [], $dataAppend = [], $batch = 100, $table = '', $prefix = '')
    {
        if (empty($data) || empty($constraints) || $batch <= 0) {
            return false;
        }
        $table   = $table ?: $this->table();
        $chunks  = array_chunk($data, $batch);
        $columns = array_keys($data[0]);
        $set     = [];
        $affected_rows = 0;

        // append more columns from the appended data
        if (!empty($dataAppend)) {
            $columns = array_merge($columns, array_keys($dataAppend));
        }

        // check UPDATE clause & columns
        foreach ($columns as $k => $column) {
            if (!in_array($column, $columnsNotUpdated)) {
                $columns[$k] = '"'.$prefix.$column.'"';
                $set[] = '"'.$prefix.$column.'" = excluded."'.$prefix.$column.'"';
            }
        }
        // check WHERE clause
        $where = $this->_prepareWhere($conditions, $table, $prefix);

        // process each chunk of records that they will be inserted or updated
        foreach ($chunks as $chunk) {
            $sql  = "INSERT INTO $table (".implode(',', $columns).")";
            $sql .= " VALUES ".implode(', ', $this->_prepareValuesForInsert($chunk, $dataAppend));
            $sql .= " ON CONFLICT (".implode(',', $constraints).")";
            $sql .= " DO ".(empty($set) ? "NOTHING " : "UPDATE SET ".implode(', ', $set));
            $sql .= $where;
            $this->db->query($sql);
            $affected_rows += $this->db->affected_rows();
        }
        // affected_rows always is 1 for both updated or inserted successfully
        return $affected_rows;
    }

    /**
     * Prepare values for insertion
     *
     * @param  array $params
     * @param  array $dataAppend
     *
     * @return array
     */
    private function _prepareValuesForInsert($params = [], $dataAppend = [])
    {
        return array_reduce($params, function ($carry, $item) use ($dataAppend) {
            $item = array_merge($item, $dataAppend);
            array_walk($item, function (&$value, $key) {
                $value = $this->db->escape($value);
            });
            $carry[] = '('.implode(',', $item).')';
            return $carry;
        });
    }

    /**
     * Prepare a string of WHERE clause
     *
     * @param  string $table
     *
     * @return string
     */
    private function _prepareWhere($conditions = [], $table = '', $prefix = '')
    {
        if (is_string($conditions)) {
            return $conditions ? " WHERE ".$conditions : '';
        }

        $prefix = (!empty($table) ? $table.'.' : '').$prefix;

        if (is_array($conditions)) {
            $whereArray = [];
            foreach ($conditions as $column => $value) {
                $whereArray[] = $prefix.$column.(is_array($value) ? " ".$value[0]." ".($value[1] ? $this->db->escape($value[1]) : "") : " = ".$this->db->escape($value));
            }
            if (!empty($whereArray)) {
                return " WHERE ".implode(' AND ', $whereArray);
            }
        }
        return '';
    }

    /**
     * Update record
     *
     * @param int   $id
     * @param array $params
     * @param array $primayKey
     *
     * @return bool
     */
    public function update($id, $params = [], $primayKey = null)
    {
        return empty($id) || empty($params) ? false : $this->db->update($this->table(), $params, [$this->primary_key($primayKey) => $id]);
    }

    /**
     * Update the multiple rows with the same values
     *
     * @param  array $params
     * @param  array $conditions
     *
     * @return bool
     */
    public function updateSameBy($params = [], $conditions = [])
    {
        if (empty($params) || empty($conditions)) {
            return false;
        }

        $this->db->where($conditions);

        return $this->db->update($this->table(), $params);
    }

    /**
     * Update the row
     *
     * @param  array $params
     * @param  array $conditions
     *
     * @return bool
     */
    public function updateBy($params = [], $conditions = [])
    {
        if (empty($params) || empty($conditions)) {
            return false;
        }

        return $this->db->where($conditions)->update($this->table(), $params);
    }

    /**
     * Update the multiple records
     *
     * @param array  $params
     * @param string $primaryKey
     *
     * @return bool
     */
    public function updateMany($params = [], $primaryKey = null)
    {
        return empty($params) ? false : $this->db->update_batch($this->table(), $params, $this->primary_key($primaryKey));
    }

    /**
     * Update the multiple records at once time by conditions
     *
     * @param array  $params
     * @param array  $conditions
     * @param string $index
     *
     * @return bool
     */
    public function updateManyBy($params = [], $conditions = [], $index = null)
    {
        // invalid request
        if (empty($params) || empty($conditions)) {
            return false;
        }

        $index = $index ?: $this->primary_key();
        foreach ($conditions as $column => $value) {
            $this->db->where($column, $value);
        }
        return $this->db->update_batch($this->table(), $params, $index);
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
     * Delete record(s) by conditions
     *
     * @param array $conditions
     * @param string $table
     *
     * @return bool
     */
    public function deleteBy($conditions = [], $table = null)
    {
        $table = $table ?: $this->table();
        return empty($conditions) ? false : $this->db->delete($table, $conditions);
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
