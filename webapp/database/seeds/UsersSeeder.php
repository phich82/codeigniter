
<?php

class UsersSeeder extends Seeder
{

    private $table = 'users';

    /**
     * Run seeder
     *
     * @return void
     */
    public function run()
    {
        $this->db->truncate($this->table);

        //seed the records manually
        $data = [
            'username' => 'admin',
            'password' => '9871'
        ];
        $this->db->insert($this->table, $data);

        //seed the records using faker
        for ($i = 0; $i < 10; $i++) {
            $data = [
                'username' => $this->faker->unique()->userName,
                'password' => '1234',
            ];
            $this->db->insert($this->table, $data);
        }
    }
}
