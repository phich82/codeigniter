<?php

require_once 'UsersSeeder.php';

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
    }
}
