
<?php

require_once 'My_Migration.php';

class Migration_create_addresses_table extends My_Migration
{
    /**
     * Create table with columns
     *
     * @return void
     */
    public function up()
    {
        $this->create_table('addresses', [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'primary_key' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'foreign_key' => [
                    'table' => 'users',
                    'field' => 'id'
                ]
            ],
            'house_no' => [
                'type' => 'VARCHAR',
                'constraint' => 150
            ],
            'ward' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'district' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'province' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true
            ]
        ], 'INNODB');
    }

    /**
     * Drop table
     *
     * @return void
     */
    public function down()
    {
        $this->dbforge->drop_table('addresses');
    }
}
        
