<?php

class MyTestCase extends TestCase
{
    protected $dbTestCase;

    public function setUp()
    {
        parent::setUp();

        $this->resetInstance();
        $this->CI->load->database();
        $this->dbTestCase = $this->CI->db;
    }

    public function seeInDatabase($table, $where = [])
    {
        $count = $this->dbTestCase->from($table)->where($where)->count_all_results();

        $this->assertTrue($count > 0, 'Row not found in database');
    }
}
