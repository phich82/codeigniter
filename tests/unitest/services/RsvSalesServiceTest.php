<?php

class RsvSalesServiceTest extends MyTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->CI->load->library('api/Rsv_sales_service', null, 'rsvSalesService');
        $this->rsvSalesService = $this->CI->rsvSalesService;
    }

    public function test_create_orders_successfully_when_input_not_empty()
    {
        $body = [1, 2];
        $headers = [];
        $result = $this->rsvSalesService->createOrders($body, $headers);

        $this->assertTrue($result);
        $this->dbTestCase->close();
        $this->dbTestCase->reconnect();
        //var_dump($this->dbTestCase->errors());
        $this->seeInDatabase('users', ['username' => 'admin']);
    }
}
