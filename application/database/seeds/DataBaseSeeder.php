<?php

require_once 'UsersSeeder.php';
require_once 'MessagesSeeder.php';

class DatabaseSeeder
{
    /**
     * Run seeders
     *
     * @return void
     */
    public function run()
    {
        (new UsersSeeder)->run();
        (new MessagesSeeder)->run();
    }
}
