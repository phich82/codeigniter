<?php

/**
 * Helpers For Class
 */
trait DBHelperTrait
{
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
     * Get table
     *
     * @return string
     */
    public static function getTable()
    {
        return property_exists(get_class(), 'table') ? static::$table : self::plural(get_class());
    }

    /**
     * Get table name
     *
     * @return string
     */
    public static function table()
    {
        return self::getTable();
    }

    /**
     * Plural of string
     *
     * @param string $str [string]
     *
     * @return string
     */
    private static function plural($str)
    {
        return is_string($str) ? strtolower($str)."s" : '';
    }
}
