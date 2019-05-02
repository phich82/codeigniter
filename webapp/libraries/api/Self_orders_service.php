<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use App\Api\Services\SelfOrdersService;

require_once APPPATH.'Api/Services/SelfOrdersService.php';

/**
 * For dependency injection (IoC)
 */
class Self_orders_service extends SelfOrdersService
{
    //
}
