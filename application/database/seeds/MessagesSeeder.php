
<?php

class MessagesSeeder extends Seeder
{

    private $table = 'messages';

    /**
     * Run seeder
     *
     * @return void
     */
    public function run()
    {
        $this->db->truncate($this->table);

        //seed using faker
        for ($i = 0; $i < 50; $i++) {
            $data = [
                'name' => $this->faker->unique()->userName,
                'message' => $this->faker->realText(50),
            ];
            $this->db->insert($this->table, $data);
        }
    }
}
        
