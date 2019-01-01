<?php
/**
 * Only support for mysql
 */
class My_Migration extends CI_Migration
{
    /**
     * Set primary key
     *
     * @param array $fields []
     *
     * @return void
     */
    public function setPrimaryKey($fields = [])
    {
        foreach ($fields as $key => $field) {
            if (isset($field['primary_key']) && $field['primary_key'] === true) {
                $this->dbforge->add_key($key, true);
            }
        }
    }

    /**
     * Set engine of table
     *
     * @param string $table  []
     * @param string $engine []
     *
     * @return void
     */
    public function setEngine($table, $engine = 'MyISAM')
    {
        $this->db->query("ALTER TABLE `$table` ENGINE = {$engine}");
    }

    /**
     * Set unique for field|column
     *
     * @param string $table  []
     * @param array  $fields []
     *
     * @return void
     */
    function setUnique($table = '', $fields = [])
    {
        foreach ($fields as $key => $field) {
            if (isset($field['unique']) && $field['unique'] === true) {
                $sql = "ALTER TABLE `$table` ADD UNIQUE (`$key`)";
                $this->db->query($sql);
            }
        }
    }

    /**
     * Set Foreign Key
     *
     * @param string $table  []
     * @param array  $fields []
     *
     * @return void
     */
    function setForeignKey($table, $fields = [])
    {
        foreach ($fields as $key => $field) {
            $table_help = "{$table}_ibfk";
            if (isset($field['foreign_key']) && !empty($field['foreign_key'])) {
                $to_table = $field['foreign_key']['table'];
                $to_field = $field['foreign_key']['field'];
                $sql = "ALTER TABLE  `$table`
                        ADD CONSTRAINT `$table_help$key`
                        FOREIGN KEY (`$key`)
                        REFERENCES `$to_table` (`$to_field`)";
                $this->db->query($sql);
            }
        }
    }

    /**
     * Create table
     *
     * @param string $table  []
     * @param array  $fields []
     * @param string $engine []
     *
     * @return void
     */
    public function create_table($table, $fields = [], $engine = 'MyISAM')
    {
        $this->dbforge->add_field($fields);
        $this->setPrimaryKey($fields);
        $this->dbforge->create_table($table, true);
        $this->setEngine($table, $engine);
        $this->setUnique($table, $fields);
        $this->setForeignKey($table, $fields);
    }
}
