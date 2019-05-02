<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use App\Api\Services\TrnPushNotificationService;

require_once APPPATH.'Api/Services/TrnPushNotificationService.php';

/**
 * For dependency injection (IoC)
 */
class T_push_notification_service extends TrnPushNotificationService
{
    //
}
