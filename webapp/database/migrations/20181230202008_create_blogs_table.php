
<?php

class Migration_create_blogs_table extends CI_Migration
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
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('blogs');
    }

    /**
     * Drop table
     *
     * @return void
     */
    public function down()
    {
        $this->dbforge->drop_table('blogs');
    }
}
        