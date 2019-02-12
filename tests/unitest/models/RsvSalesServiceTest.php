<?php

class RsvSalesServiceTest extends TestCase
{
    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->library('api/Rsv_sales_service', null, 'rsvSalesService');
        $this->rsvSalesService = $this->CI->rsvSalesService;
    }

    public function test_create_orders_successfully_when_input_not_empty()
    {
        $body = [1, 2];
        $headers = [];
        $result = $this->rsvSalesService->createOrders($body, $headers);
        $this->assertTrue($result);
    }
}
