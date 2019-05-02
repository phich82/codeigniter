<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use App\Api\Services\MstPushService;

require_once APPPATH.'Api/Services/MstPushService.php';

/**
 * For dependency injection (IoC)
 */
class M_push_service extends MstPushService
{
    //
}
