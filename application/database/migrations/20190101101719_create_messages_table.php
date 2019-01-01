
<?php

class Migration_create_messages_table extends CI_Migration
{
    /**
     * Create table with columns
     *
     * @return void
     */
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false
            ],
            'message' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type' => 'SMALLINT',
                'constraint' => 1,
                'default' => 0
            ],
        ]);
        $this->dbforge->add_field("`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        $this->dbforge->add_field("`updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('messages');
    }

    /**
     * Drop table
     *
     * @return void
     */
    public function down()
    {
        $this->dbforge->drop_table('messages');
    }
}
        
