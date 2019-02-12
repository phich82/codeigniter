
<?php

class Migration_create_users_table extends CI_Migration
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
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ]
        ]);
        
        $attributes = ['ENGINE' => 'InnoDB', 'CHARACTER SET' => 'utf8', 'COLLATE' => 'utf8_general_ci'];
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('users', true, $attributes);
    }

    /**
     * Drop table
     *
     * @return void
     */
    public function down()
    {
        $this->dbforge->drop_table('users');
    }
}
