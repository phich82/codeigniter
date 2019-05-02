<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use App\Api\Services\AwsConnectCloudService;

require_once APPPATH.'Api/Services/AwsConnectCloudService.php';

/**
 * For dependency injection (IoC)
 */
class Aws_connect_cloud_service extends AwsConnectCloudService
{
    //
}
